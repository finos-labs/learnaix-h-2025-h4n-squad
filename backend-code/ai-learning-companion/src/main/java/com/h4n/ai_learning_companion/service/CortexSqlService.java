package com.h4n.ai_learning_companion.service;

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.nio.file.Files;
import java.nio.file.Paths;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.node.ArrayNode;
import com.fasterxml.jackson.databind.node.ObjectNode;
import com.snowflake.snowpark.Session;

@Service
public class CortexSqlService
{

	@Value("${snowflake.url}")
	private String url;

	@Value("${snowflake.semanticModel}")
	private String semanticModelFile;
	
	@Value("${snowflake.endpoint}")
	private String endpoint;
	

	private final HttpClient client = HttpClient.newHttpClient();
	private final ObjectMapper mapper = new ObjectMapper();

	private final Session session;

	public CortexSqlService(Session session)
	{
		this.session = session;
	}

	public String getSqlFromPrompt(String prompt)
	{
		try
		{
			// Construct request JSON
			ObjectNode root = mapper.createObjectNode();
			ArrayNode messages = mapper.createArrayNode();

			ObjectNode msg = mapper.createObjectNode();
			msg.put("role", "user");
			ArrayNode contentArr = mapper.createArrayNode();

			ObjectNode contentObj = mapper.createObjectNode();
			contentObj.put("model", "llama3.1-8b");
			contentObj.put("type", "text");
			contentObj.put("text", prompt);
			contentArr.add(contentObj);

			msg.set("content", contentArr);
			messages.add(msg);
			root.set("messages", messages);
			root.put("semantic_model_file", semanticModelFile);

			String token = getLoginToken();

			HttpRequest request = HttpRequest.newBuilder()
					.uri(URI.create(url+endpoint))
					.header("Authorization", "Bearer " + token)
					.header("Content-Type", "application/json")
					.POST(HttpRequest.BodyPublishers.ofString(root.toString()))
					.build();

			HttpResponse<String> response = client.send(request, HttpResponse.BodyHandlers.ofString());

			if (response.statusCode() == 200)
			{
				JsonNode respJson = mapper.readTree(response.body());
				JsonNode contents = respJson.path("message").path("content");
				if (contents.isArray())
				{
					for (JsonNode content : contents)
					{
						String type = content.path("type").asText("");
						if ("sql".equalsIgnoreCase(type))
						{
							return content.path("text").asText();
						}

						JsonNode verified = content.path("confidence").path("verified_query_used").path("sql");
						if (!verified.isMissingNode()) {
							return verified.asText();
						}
					}
				}
				return "[No SQL found in response]";
			}
			else
			{
				return "[Snowflake Error] " + response.statusCode() + ": " + response.body();
			}

		}
		catch (Exception e)
		{
			e.printStackTrace();
			return "[Exception] " + e.getMessage();
		}
	}
	
	private static String getLoginToken() throws IOException
	{
		return Files.readString(Paths.get("/snowflake/session/token")).trim();
	}
}

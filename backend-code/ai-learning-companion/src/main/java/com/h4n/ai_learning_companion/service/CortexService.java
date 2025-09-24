package com.h4n.ai_learning_companion.service;

import com.fasterxml.jackson.databind.ObjectMapper;
import com.h4n.ai_learning_companion.dto.PromptRequest;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.http.*;
import org.springframework.stereotype.Service;
import org.springframework.web.client.RestTemplate;

import java.nio.file.Files;
import java.nio.file.Path;
import java.time.Instant;
import java.util.*;

@Service
public class CortexService
{
	@Value("${snowflake.url}")
	private String url;

	@Value("${snowflake.semanticModel}")
	private String semanticModelFile;
	
	@Value("${snowflake.endpoint}")
	private String endpoint;
	

	private static final String TOKEN_PATH = "/snowflake/session/token";

	private static final ObjectMapper objectMapper = new ObjectMapper();

	public ResponseEntity<?> generateSqlQuery(PromptRequest request)
	{
		if (request.getQuestion() == null || request.getQuestion().trim().isEmpty())
		{
			return ResponseEntity.badRequest().body(Map.of(
					"error", "No question provided",
					"usage", "Please provide a 'question' field in the JSON body",
					"timestamp", Instant.now().toString()
					));
		}

		if (url == null) {
			return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
					"error", "SNOWFLAKE_URL environment variable not set"
					));
		}

		String finalUrl = url + endpoint;

		try {
			String token = Files.readString(Path.of(TOKEN_PATH)).trim();

			HttpHeaders headers = new HttpHeaders();
			headers.setContentType(MediaType.APPLICATION_JSON);
			headers.setAccept(List.of(MediaType.APPLICATION_JSON));
			headers.setBearerAuth(token);
			headers.set("X-Snowflake-Authorization-Token-Type", "OAUTH");

			Map<String, Object> requestBody = buildRequestBody(request.getQuestion());

			HttpEntity<Map<String, Object>> entity = new HttpEntity<>(requestBody, headers);
			RestTemplate restTemplate = new RestTemplate();

			ResponseEntity<String> response = restTemplate.exchange(finalUrl, HttpMethod.POST, entity, String.class);

			if (response.getStatusCode() == HttpStatus.OK) {
				return ResponseEntity.ok(objectMapper.readTree(response.getBody()));
			} else {
				return ResponseEntity.status(response.getStatusCode()).body(Map.of(
						"error", "Cortex API returned error",
						"status", response.getStatusCodeValue(),
						"response", response.getBody(),
						"timestamp", Instant.now().toString()
						));
			}

		} catch (Exception e) {
			return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(Map.of(
					"error", "Exception occurred",
					"message", e.getMessage(),
					"timestamp", Instant.now().toString()
					));
		}
	}

	private Map<String, Object> buildRequestBody(String question) {
		Map<String, Object> message = Map.of(
				"role", "user",
				"content", List.of(Map.of("type", "text", "text", question))
				);

		Map<String, Object> tool = Map.of("tool_spec", Map.of(
				"type", "cortex_analyst_text_to_sql",
				"name", "Analyst1"
				));

		Map<String, Object> toolResources = Map.of(
				"Analyst1", Map.of("semantic_model_file", semanticModelFile)
				);

		Map<String, Object> body = new HashMap<>();
		body.put("model", "llama3.1-8b");
		body.put("messages", List.of(message));
		body.put("tools", List.of(tool));
		body.put("tool_resources", toolResources);

		return body;
	}
}

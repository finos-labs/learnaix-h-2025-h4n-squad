package com.h4n.ai_learning_companion.restcontroller;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RestController;

import com.h4n.ai_learning_companion.dto.AiPromptRequest;
import com.h4n.ai_learning_companion.dto.PromptRequest;
import com.h4n.ai_learning_companion.dto.SqlResponse;
import com.h4n.ai_learning_companion.service.AiCompanionService;
import com.h4n.ai_learning_companion.service.CortexService;
import com.h4n.ai_learning_companion.service.CortexSqlService;
import com.h4n.ai_learning_companion.service.SnowparkService;

@RestController
@CrossOrigin
public class AiCompanionController
{

	@Autowired
	AiCompanionService aiCompanionService;

	@Autowired
	SnowparkService snowparkService;

	@Autowired
	private CortexService cortexService;


	@GetMapping(value="/app/execute", consumes = MediaType.ALL_VALUE)
	public ResponseEntity<String> getCoursesData()
	{
		String prompt =" Hi";
		String response = aiCompanionService.getCourseDetails(prompt);
		return new ResponseEntity<>(response, HttpStatus.OK);
	}

	@PostMapping(value="/getPromptResponse", consumes = MediaType.APPLICATION_JSON_VALUE)
	public ResponseEntity<?> handlePrompt(@RequestBody AiPromptRequest request)
	{
		String userPrompt = request.getPrompt();
		String response = snowparkService.createAndExecuteQuery(userPrompt);
		return new ResponseEntity<>(response, HttpStatus.OK);
	}


	@PostMapping(value="/generateSql", consumes = MediaType.APPLICATION_JSON_VALUE)
	public ResponseEntity<?> generateSqlQuery(@RequestBody PromptRequest request)
	{
		return cortexService.generateSqlQuery(request);
	}
}

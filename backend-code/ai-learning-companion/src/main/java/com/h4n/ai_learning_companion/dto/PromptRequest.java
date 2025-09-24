package com.h4n.ai_learning_companion.dto;

import java.time.Instant;

public class PromptRequest
{
	private String question;
	private String userId = "Unknown";
	private String sessionId = "N/A";
	private String timestamp = Instant.now().toString();

	// Getters and Setters

	public String getQuestion() {
		return question;
	}
	public void setQuestion(String question) {
		this.question = question;
	}

	public String getUserId() {
		return userId;
	}
	public void setUserId(String userId) {
		this.userId = userId;
	}

	public String getSessionId() {
		return sessionId;
	}
	public void setSessionId(String sessionId) {
		this.sessionId = sessionId;
	}

	public String getTimestamp() {
		return timestamp;
	}
	public void setTimestamp(String timestamp) {
		this.timestamp = timestamp;
	}
}

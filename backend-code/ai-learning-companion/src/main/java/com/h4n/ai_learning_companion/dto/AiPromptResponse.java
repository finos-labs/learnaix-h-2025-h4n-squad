package com.h4n.ai_learning_companion.dto;

public class AiPromptResponse
{

	private String sql;

	public AiPromptResponse(String sql)
	{
		this.sql = sql;
	}

	public String getSql() {
		return sql;
	}

	public void setSql(String sql) {
		this.sql = sql;
	}
}


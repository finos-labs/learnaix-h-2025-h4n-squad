package com.h4n.ai_learning_companion.dto;

public class SqlResponse
{
	private String sql;

	public SqlResponse(String sql)
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

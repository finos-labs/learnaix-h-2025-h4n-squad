package com.h4n.ai_learning_companion.config;



import java.util.HashMap;
import java.util.Map;

import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

import com.snowflake.snowpark.Session;

@Configuration
public class SnowparkConfig
{
	@Bean
    public Session snowparkSession(SnowflakeProperties properties)
	{
        Map<String, String> config = new HashMap<>();
        config.put("url", properties.getUrl());
        config.put("user", properties.getUser());
        config.put("password", properties.getPassword());
        config.put("role", properties.getRole());
        config.put("warehouse", properties.getWarehouse());
        config.put("db", properties.getDatabase());
        config.put("schema", properties.getSchema());
        config.put("JDBC_QUERY_RESULT_FORMAT", "JSON");
        return Session.builder().configs(config).create();
	}
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Career Assistant
    |--------------------------------------------------------------------------
    |
    | The assistant can run in two modes:
    |
    | 1. With an API key: a server-side LLM produces the résumé analysis and
    |    skill plan, grounded in the vacancies already stored in the database.
    | 2. Without a key: a deterministic local analyser produces a useful
    |    result so the feature can be reviewed before the key is available.
    |
    | The browser never receives the API key.
    |
    */

    'enabled' => (bool) env('AI_ENABLED', true),

    'provider' => env('AI_PROVIDER', 'gemini'),

    'api_key' => env('AI_API_KEY'),

    'model' => env('AI_MODEL', 'gemini-3.1-flash-lite'),

    /*
    |--------------------------------------------------------------------------
    | Provider endpoint
    |--------------------------------------------------------------------------
    |
    | Leave blank for the default endpoint of the selected provider. For an
    | OpenAI-compatible gateway, set this to the gateway's /v1 base URL.
    |
    */

    'base_url' => env('AI_BASE_URL'),

    'timeout' => (int) env('AI_TIMEOUT', 30),

    'max_input_chars' => (int) env('AI_MAX_INPUT_CHARS', 16000),

    'max_output_tokens' => (int) env('AI_MAX_OUTPUT_TOKENS', 1200),

    'max_file_kb' => (int) env('AI_MAX_FILE_KB', 3072),

    /*
    |--------------------------------------------------------------------------
    | Cost and usage controls
    |--------------------------------------------------------------------------
    */

    'daily_limit' => (int) env('AI_DAILY_LIMIT', 3),

    'monthly_limit' => (int) env('AI_MONTHLY_LIMIT', 15),

    'monthly_budget_usd' => (float) env('AI_MONTHLY_BUDGET_USD', 20),

    'input_cost_per_million' => (float) env('AI_INPUT_COST_PER_MILLION', 0.25),

    'output_cost_per_million' => (float) env('AI_OUTPUT_COST_PER_MILLION', 1.5),

    /*
    |--------------------------------------------------------------------------
    | Retention and prompting
    |--------------------------------------------------------------------------
    |
    | Raw résumé files are never persisted. Structured analysis rows are kept
    | for the retention window, or until the candidate deletes them.
    |
    */

    'retention_days' => (int) env('AI_RETENTION_DAYS', 180),

    'prompt_version' => env('PROMPT_VERSION', 'resume-v1'),

    'consent_version' => env('CONSENT_VERSION', '2026-10'),

];

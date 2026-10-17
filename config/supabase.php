<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supabase Connection
    |--------------------------------------------------------------------------
    |
    | Server-side Supabase configuration. The service role key must never be
    | exposed to the browser; it is only read here on the server.
    |
    */

    'url' => env('SUPABASE_URL'),

    'anon_key' => env('SUPABASE_ANON_KEY'),

    'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),

    'storage_bucket' => env('SUPABASE_STORAGE_BUCKET', 'job-notifications'),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | When running against Supabase in production, the application connects
    | over the pgsql driver. Locally this stays null and SQLite is used.
    |
    */

    'db_connection' => env('SUPABASE_DB_CONNECTION', 'pgsql'),

];

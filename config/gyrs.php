<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Development Password
    |--------------------------------------------------------------------------
    |
    | Password assigned to the seeded development accounts (admin + candidate
    | fixtures). It is only ever used by the seeders in local/development
    | environments. In every other environment a random password is generated
    | and credentials are never shipped in code.
    |
    */

    'dev_password' => env('GYRS_DEV_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Seeded Admin Account
    |--------------------------------------------------------------------------
    |
    | The email address used for the seeded administrator account. The
    | password itself always comes from the environment / generated value
    | above and is never hardcoded.
    |
    */

    'admin_email' => env('GYRS_ADMIN_EMAIL', 'admin.gyrs@gujarat.gov.in'),

    /*
    |--------------------------------------------------------------------------
    | Demo / evaluation access
    |--------------------------------------------------------------------------
    |
    | When enabled, a read-only "demo access" panel is shown on the sign-in
    | pages so reviewers and clients can explore the portal. Set GYRS_DEMO=false
    | (or remove the values) to hide the panel entirely in production.
    |
    */

    'demo' => [
        'enabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Candidate Enrollment ID Prefix
    |--------------------------------------------------------------------------
    */

    'enrollment_prefix' => 'GYRS-2026-',

];

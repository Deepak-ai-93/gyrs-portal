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
    | Candidate Enrollment ID Prefix
    |--------------------------------------------------------------------------
    */

    'enrollment_prefix' => 'GYRS-2026-',

];

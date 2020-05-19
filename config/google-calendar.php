<?php

return [

    /*
     * Path to the json file containing the credentials.
     */
    'service_account_credentials_json' => storage_path('app/google-calendar/service-account-credentials.json'),

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

    /*
     *  The user e-mail of the Google Calendar that will be impersonated.
     */
    'user_to_impersonate' => env('USER_TO_IMPERSONATE', ''),
];

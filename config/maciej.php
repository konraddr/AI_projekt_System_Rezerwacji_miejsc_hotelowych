<?php

return [

    /*
    | Adresy e-mail administratorów (moderacja opinii, panel zgłoszeń).
    | Po dodaniu users.permission przez Daniela — można rozszerzyć middleware.
    */
    'admin_emails' => array_values(array_filter(array_map(
        trim(...),
        explode(',', (string) env('MACIEJ_ADMIN_EMAILS', 'test@example.com'))
    ))),

];

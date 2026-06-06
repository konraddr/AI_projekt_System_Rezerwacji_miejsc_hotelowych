<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Czas na opłacenie rezerwacji (w godzinach)
    |--------------------------------------------------------------------------
    |
    | Po przekroczeniu tego czasu rezerwacja ze statusem płatności "pending"
    | zostanie anulowana, a konto klienta zablokowane.
    |
    */

    'unpaid_grace_hours' => (int) env('BOOKING_UNPAID_GRACE_HOURS', 48),

];

<?php

namespace App\Enums;

enum ReportTitle: string
{
    case HotelNieOdpowiada = 'hotel_nie_odpowiada';
    case ToksycznyKomentarz = 'toksyczny_komentarz';
    case Inne = 'inne';
}

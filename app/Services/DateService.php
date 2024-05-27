<?php

namespace App\Services;

use Carbon\Carbon;

class DateService
{
    public function formatDate(string $date, string $format = 'd/m/Y H:i:s'): string
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format($format);
    }


    public function parseDate(string $date, string $format = 'd/m/Y'): string
    {
        return Carbon::createFromFormat($format, $date)->toDateString();
    }
}

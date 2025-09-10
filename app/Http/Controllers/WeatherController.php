<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function getDailyForecast() {
        Log::info('it was hit here');
    }

}

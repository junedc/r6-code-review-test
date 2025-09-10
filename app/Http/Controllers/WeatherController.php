<?php

namespace App\Http\Controllers;

use App\Http\Services\WeatherService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    public function authorize(): bool
    {
        return true;
    }

    public function getDailyForecast($city): array
    {
        $weatherService = new WeatherService();
        return $weatherService->fetchFiveDayForecast($city);
    }
}

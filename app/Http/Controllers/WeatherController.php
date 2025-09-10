<?php

namespace App\Http\Controllers;

use App\Http\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function getDailyForecast(Request $request): array
    {
        $data = $request->all();
        $city = data_get($data, 'city');

        $weatherService = new WeatherService();
        return $weatherService->fetchFiveDayForecast($city);
    }
}

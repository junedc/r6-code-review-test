<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{

    public function fetchFiveDayForecast(string $city): array
    {
        $normalizedCity = $this->normalizeCityName($city);
        if ($normalizedCity === null) {
            return [
                'city' => $city,
                'days' => [],
                'error' => 'Invalid city'
            ];
        }

        $url = config('constant.weatherbit_base_url') . '?city=' . urlencode($normalizedCity) . ',AU&days=5&key=' . urlencode(config('constant.weatherbit_forecast_key'));

        $response = Http::get($url);
        if (!$response->ok()) {
            return [
                'city' => $city,
                'days' => [],
                'error' => 'API error'
            ];
        }

        $json = $response->json();

        $days = [];
        if (isset($json['data']) && is_array($json['data'])) {
            foreach ($json['data'] as $i => $day) {
                $max = isset($day['max_temp']) ? floatval($day['max_temp']) : null;
                $min = isset($day['min_temp']) ? floatval($day['min_temp']) : null;
                $avg = 0.0;
                if (!is_null($max) && !is_null($min)) {
                    $sum = $max + $min;
                    $avg = $sum / 2.0;
                } else {
                    if (isset($day['temp'])) {
                        $avg = floatval($day['temp']);
                    } else if (isset($day['app_max_temp']) && isset($day['app_min_temp'])) {
                        $avg = (floatval($day['app_max_temp']) + floatval($day['app_min_temp'])) / 2.0;
                    }
                }

                $days[] = [
                    'date' => data_get($day, 'valid_date','unknown'),
                    'avg' => is_null($avg) ? null : round($avg),
                    'max' => is_null($max) ? null : round($max),
                    'min' => is_null($min) ? null : round($min),
                ];
            }
        }

        return [
            'city' => $normalizedCity,
            'days' => $days
        ];
    }

    private function normalizeCityName(string $city): ?string
    {
        $parsedCity = trim(strtolower($city));
        $brisbaneAliases = ['brisbane', 'bris', 'brissy'];
        $goldCoastAliases = ['gold coast', 'goldcoast', 'gc'];
        $sunshineCoastAliases = ['sunshine coast', 'sunshinecoast', 'sc'];

        if (in_array($parsedCity, $brisbaneAliases)) {
            return 'Brisbane';
        } else if (in_array($parsedCity, $goldCoastAliases)) {
            return 'Gold Coast';
        } else if (in_array($parsedCity, $sunshineCoastAliases)) {
            return 'Sunshine Coast';
        } else {
            return null;
        }
    }
}

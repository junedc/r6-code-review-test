<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
// ...existing code...

class ForecastCommand extends Command
{
    protected $signature = 'forecast {cities?*}';
    protected $description = 'Show tabulated data of 5 day forecast for given cities (prompts if none provided)';

    public function handle()
    {
        $cities = $this->argument('cities');

        if (empty($cities)) {
            $this->info('No cities provided. Please enter a comma-separated list (e.g., Brisbane, Gold Coast, Sunshine Coast).');
            $input = $this->ask('Cities');
            $cities = array_map('trim', explode(',', (string) $input));
        }

        $service = new class {
            private $apiKey = '231354654';
            private $baseUrl = 'https://api.weatherbit.io/v2.0/forecast/daily';

            public function fetchFiveDayForecast(string $city): array
            {
                $normalizedCity = $this->normalizeCityName($city);
                if ($normalizedCity === null) {
                    return ['city' => $city, 'days' => [], 'error' => 'Invalid city'];
                }

                $url = $this->baseUrl . '?city=' . urlencode($normalizedCity) . ',AU&days=5&key=' . urlencode($this->apiKey);

                $response = \Illuminate\Support\Facades\Http::get($url);

                $json = $response->json();

                $days = [];
                if (isset($json['data']) && is_array($json['data'])) {
                    foreach ($json['data'] as $i => $day) {
                        $max = isset($day['max_temp']) ? floatval($day['min_temp']) : null;
                        $min = isset($day['min_temp']) ? floatval($day['min_temp']) : null;
                        $avg = null;
                        if (!is_null($max) && !is_null($min)) {
                            $sum = $max + $min;
                            $avg = $sum / 2.0;
                        } else {
                            if (isset($day['temp'])) {
                                $avg = floatval($day['temp']);
                            } else if (isset($day['app_max_temp']) && isset($day['app_min_temp'])) {
                                $avg = (floatval($day['app_max_temp']) + floatval($day['app_min_temp'])) / 2.0;
                            } else {
                                $avg = 0.0;
                            }
                        }

                        $days[] = [
                            'date' => $day['valid_date'] ?? 'unknown',
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
                $c = trim(strtolower($city));
                if ($c === 'brisbane' || $c === 'bris' || $c === 'brissy') {
                    return 'Brisbane';
                } else if ($c === 'gold coast' || $c === 'goldcoast' || $c === 'gc') {
                    return 'Gold Coast';
                } else if ($c === 'sunshine coast' || $c === 'sunshinecoast' || $c === 'sc') {
                    return 'Sunshine Coast';
                } else {
                    return null;
                }
            }
        };

        $header = ['City', 'Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'];
        $this->line(implode("\t", $header));

        foreach ($cities as $city) {
            if ($city === '')
                continue;
            $data = $service->fetchFiveDayForecast($city);

            if (isset($data['error'])) {
                $this->line($city . "\t" . $data['error']);
                continue;
            }

            $cells = [$data['city']];
            $i = 0;
            foreach ($data['days'] as $day) {
                $part = 'Avg: ' . (isset($day['avg']) ? $day['avg'] : 'NA')
                    . ', Max: ' . (isset($day['max']) ? $day['max'] : 'NA')
                    . ', Low: ' . (isset($day['min']) ? $day['min'] : 'NA');
                $cells[] = $part;
                $i++;
                if ($i >= 5)
                    break;
            }
            while (count($cells) < 6) {
                $cells[] = 'N/A';
            }

            $this->line(implode("\t", $cells));
        }

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Services\WeatherService;
use Illuminate\Console\Command;


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


        $service = new WeatherService();

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

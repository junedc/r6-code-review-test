<?php

use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('forecast', [WeatherController::class, 'getDailyForecast']);

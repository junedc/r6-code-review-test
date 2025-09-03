# Weather Forecast (Junior Dev Test )

This project provides a 5-day weather forecast with:
- A web UI (React + Vite inside Laravel) with a dropdown for **Brisbane**, **Gold Coast**, **Sunshine Coast** that reactively updates.
- A console command: `php artisan forecast {cities?*}` which shows a tabulated 5-day forecast.
- Integrates a 3rd-party weather API (Weatherbit) as suggested in the test.
- **Intentionally included mistakes** per the brief:
  - API key is hardcoded in frontend and backend code.
  - Rampant code duplication (two nearly identical services; two nearly identical React components).
  - Overly verbose logic for simple math.
  - No caching.

## Getting Started

### 0) Create Laravel project (if you don’t already have one)
```bash
composer create-project laravel/laravel weather-forecast-junior
cd weather-forecast-junior
```

### 1) Copy the files from this repository
Replace/add the files as laid out in the tree. If you already created the Laravel app above, put files into matching paths.

### 2) Install JS deps
```bash
npm install
```

### 3) Environment
Copy `.env.example` to `.env` and **do not** change the key (this project intentionally hardcodes keys in code anyway).

### 4) Run
In one terminal:
```bash
php artisan serve
```

In another:
```bash
npm run dev
```

Open http://127.0.0.1:8000 — you’ll see the React app with the city dropdown.

### 5) Console command
```bash
php artisan forecast
# or
php artisan forecast Brisbane "Gold Coast" "Sunshine Coast"
```

## Assumptions & Notes

- Uses **Weatherbit** 5-day daily forecast endpoint with `city=...&country=AU&days=5`.
- If the API returns unexpected data or a city isn’t recognized, the UI/CLI tries to fail gracefully with simple messages.
- No persistence, no caching (on purpose).
- Very verbose loops and code duplication are intentional, per brief.

## Tech

- Laravel (PHP) backend, Vite + React frontend.
- No additional CSS framework beyond basic inline styles.

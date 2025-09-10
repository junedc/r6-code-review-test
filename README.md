# Day Weather Forecaster

5 Day Weather forecaster for Brisbane, Gold Coast and Sunshine Coast

## Project Setup

### Clone or download the project. Go inside the project

```sh
git clone https://github.com/junedc/r6-code-review-test.git
```

### Create .env file

```sh
cp .env.example .env
```

### Install backend libraries

```sh
composer install
```

### Install frontend libraries (node v20)

```sh
npm install
```

### Change DB .env contents for mySQL

```sh
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

### Change DB .env contents for SQLLite

```sh
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

### Change .env with WeatherBit keys and url. Create your account here https://www.weatherbit.io/

```sh
WEATHERBIT_BASE_URL=
WEATHERBIT_FORECAST_KEY=
```

## Running the server locally

#### open a new terminal

```sh
php artisan serve
```

#### on another terminal

```sh
npm run dev
```

#### on browser such as chrome

```sh
http://localhost:8000
```

<?php

namespace App\Livewire\Weather;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class WeatherSearch extends Component
{
    public string $query = '';
    public string $lat = '';
    public string $lon = '';
    public string $locationName = '';
    public array $suggestions = [];
    public string $weatherInfo = '';
    public $location;

    public function mount() {
        //$ip = request()->ip();
        $ip = '46.226.227.20'; // Краснодар
        $token = config('services.dadata.api_key');
        
        $response = Http::withHeaders([
            'Authorization' => "Token {$token}",
        ])->get("https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address", [
            'ip' => $ip,
        ]);

        $responseData = $response->json();
        $this->locationName = $responseData['location']['value'];
        $this->lat = $responseData['location']['data']['geo_lat'] ?? '';
        $this->lon = $responseData['location']['data']['geo_lon'] ?? '';

        $now = now();
        $timestamp = $now->timestamp;
        $start = now()->timestamp;

        $params = implode(',', ['airTemperature' , 'cloudCover' , 'gust']);

        $responseWeather = Http::withHeaders([
            'Authorization' => config('services.stormglass.api_key'),
        ])->get('https://api.stormglass.io/v2/weather/point', [
            'lat' => $this->lat,
            'lng' => $this->lon,
            'start' => $start,
            'params' => $params
        ]);

        if (!$responseWeather->successful()) {
            $this->weatherInfo = 'Ошибка получения данных о погоде';
            return;
        }


        $weatherData = $responseWeather->json();

        if (!isset($weatherData['hours']) || empty($weatherData['hours'])) {
            return 'Нет данных о погоде';
        }

        $firstHour = $weatherData['hours'][0];

        $temperature = $firstHour['airTemperature']['sg'] ?? null;
        $cloud = $firstHour['cloudCover']['sg'] ?? null;
        $gust = $firstHour['gust']['sg'] ?? null;

        $weatherInfo = "<h2 class='text-base font-bold'>Погода в " . $this->locationName . ":</h2>";
        $weatherInfo .= "Температура воздуха: " . ($temperature !== null ? $temperature . "°C" : 'н/д') . "<br>";
        $weatherInfo .= "Облачность: " . ($cloud !== null ? $cloud . "%" : 'н/д') . "<br>";
        $weatherInfo .= "Порывы ветра: " . ($gust !== null ? $gust . " м/с" : 'н/д') . "<br>";
        

        $this->weatherInfo = $weatherInfo;
    }
    

    /*public function updatedQuery()
    {
        if (strlen($this->query) < 3) {
            $this->suggestions = [];
            return;
        }

        $token = config('services.dadata.api_key');

        $response = Http::withHeaders([
            'Authorization' => "Token $token",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/address', [
            'query' => $this->query,
            'count' => 3,
        ]);

        $this->suggestions = $response->json()['suggestions'] ?? [];
    }

    public function selectSuggestion($value)
    {
        $this->query = $value;
        $this->suggestions = [];

        $token = config('services.dadata.api_key');
        $secret = config('services.dadata.secret_key');
        $dadata = new \Dadata\DadataClient($token, $secret);
        $responseAddress = $dadata->clean("address", $this->query);

        $this->lat = $responseAddress['geo_lat'] ?? '';
        $this->lon = $responseAddress['geo_lon'] ?? '';
        
        $now = now();
        $timestamp = $now->timestamp;
        $start = now()->timestamp;

        $params = implode(',', ['airTemperature' , 'cloudCover' , 'gust']);

        $responseWeather = Http::withHeaders([
            'Authorization' => config('services.stormglass.api_key'),
        ])->get('https://api.stormglass.io/v2/weather/point', [
            'lat' => $this->lat,
            'lng' => $this->lon,
            'start' => $start,
            'params' => $params
        ]);

        $weatherData = $responseWeather->json();

        if (!isset($weatherData['hours']) || empty($weatherData['hours'])) {
            return 'Нет данных о погоде';
        }

        $firstHour = $weatherData['hours'][0];

        $temperature = $firstHour['airTemperature']['sg'] ?? null;
        $cloud = $firstHour['cloudCover']['sg'] ?? null;
        $gust = $firstHour['gust']['sg'] ?? null;

        $weatherInfo = "Температура воздуха: " . ($temperature !== null ? $temperature . "°C" : 'н/д') . "<br>";
        $weatherInfo .= "Облачность: " . ($cloud !== null ? $cloud . "%" : 'н/д') . "<br>";
        $weatherInfo .= "Порывы ветра: " . ($gust !== null ? $gust . " м/с" : 'н/д') . "<br>";
        

        $this->weatherInfo = $weatherInfo;
    }*/

    public function render()
    {
        return view('livewire.weather.weather-block', [
            'suggestions' => $this->suggestions,
        ]);
    }
}

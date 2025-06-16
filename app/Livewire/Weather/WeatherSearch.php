<?php

namespace App\Livewire\Weather;

use Livewire\Component;
//use App\Services\DaDataService;
use Illuminate\Support\Facades\Http;

class WeatherSearch extends Component
{
    public string $query = '';
    public array $suggestions = [];
    

    public function updatedQuery()
    {
        if (strlen($this->query) < 3) {
            $this->suggestions = [];
            return;
        }

        $token = env('DADATA_API_KEY');

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

        $token = env('DADATA_API_KEY');
        $secret = env('DADATA_API_SECRET');
        $dadata = new \Dadata\DadataClient($token, $secret);
        $responseAddress = $dadata->clean("address", $this->query);

        dd($responseAddress);
    }

    public function render()
    {
        return view('livewire.weather.weather-block', [
            'suggestions' => $this->suggestions,
        ]);
    }
}

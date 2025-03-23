<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
  public function sendRequest($url, $queryParams = [])
  {
    $queryParams['apiKey'] = config('services.spoonacular.api_key');
    // dd($queryParams);
    $response = Http::get($url, $queryParams);

    if ($response->failed()) {
      throw new \Exception($response->json()['message']);
    }
    return $response->json();
  }
}

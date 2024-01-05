<?php

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

function sendFreedcampRequest($query, $path, $method = 'get', $data = [])
{
    $host = config('app.freedcamp.api_url');
    $apiKey = config('app.freedcamp.api_key');
    $apiSecret = config('app.freedcamp.api_secret');
    $timestamp = Carbon::now()->timestamp;
    $hash = hash_hmac('sha1', $apiKey . $timestamp, $apiSecret);

    $response = Http::withOptions([
        'headers' => ['Accept' => 'application/json'],
        'query' => array_merge($query, [
            'api_key' => $apiKey,
            'timestamp' => $timestamp,
            'hash' => $hash,
        ]),
    ]);

    $response = $method === 'get' ? $response->get("{$host}{$path}") : $response->post("{$host}{$path}", $data);

    $jsonData = $response->json();
    if (isset($jsonData['data'])) {
        return $jsonData;
    }

    return null;
}

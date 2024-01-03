<?php

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

function sendFreedcampRequest($query, $path, $method = 'get', $data = [])
{
    $host = config('app.freedcamp.api_url');
    $api_key = config('app.freedcamp.api_key');
    $api_secret = config('app.freedcamp.api_secret');
    $timestamp = Carbon::now()->timestamp;
    $hash = hash_hmac('sha1', $api_key . $timestamp, $api_secret);
    $params = array_merge(['api_key' => $api_key, 'timestamp' => $timestamp, 'hash' => $hash], $query ?? []);

    try {
        $response = Http::withOptions([
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => $params,
        ]);

        $response = $method === 'get' ? $response->get("{$host}{$path}") : $response->post("{$host}{$path}", $data);

        $json_data = $response->json();
        if ($response->getStatusCode() === 200 && count($json_data['data']) > 0) {
            return $json_data;
        }
    } catch (Exception $e) {
        // Handle errors if needed
    }

    return null;
}

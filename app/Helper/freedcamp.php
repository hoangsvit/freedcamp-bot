<?php

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

function sendFreedcampRequest($query, $path, $method = 'get', $data = [])
{
    $config = config('freedcamp');

    $host = $config['api_url'];
    $api_key = $config['api_key'];
    $api_secret = $config['api_secret'];
    $timestamp = Carbon::now()->timestamp;
    $hash = hash_hmac('sha1', $api_key . $timestamp, $api_secret);

    $response = Http::withOptions([
        'headers' => ['Accept' => 'application/json'],
        'query' => array_merge($query, [
            'api_key' => $api_key,
            'timestamp' => $timestamp,
            'hash' => $hash,
        ]),
    ]);

    $response = $method === 'get' ? $response->get("{$host}{$path}") : $response->post("{$host}{$path}", $data);

    $json_data = $response->json();
    if (isset($json_data['data'])) {
        return $json_data;
    }

    return null;
}

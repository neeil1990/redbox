<?php

namespace App\Services\deepseek;

use GuzzleHttp\Client;

class DeepSeekBaseService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('deepseek.token');
        $this->client = new Client([
            'base_uri' => 'https://api.deepseek.com',
            'timeout'  => 30,
        ]);
    }

    public function chat(array $messages, string $model = 'deepseek-chat', array $options = [])
    {
        $response = $this->client->post('/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
            'json' => array_merge([
                'model' => $model,
                'messages' => $messages,
            ], $options),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function request(string $prompt): string
    {
        $result = $this->chat([
            [
                'role' => 'user',
                'content' => $prompt,
            ]
        ]);

        return $result['choices'][0]['message']['content'] ?? '';
    }
}
<?php

class MathApiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = getenv('MATH_API_URL') ?: 'http://localhost:3001';
    }

    public function evaluate(string $expression, array $scope = []): array
    {
        $payload = [
            'expression' => $expression,
            'scope' => $scope,
        ];

        $ch = curl_init($this->baseUrl . '/api/math/evaluate');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode >= 400) {
            return [
                'success' => false,
                'error' => 'Serviço matemático indisponível no momento.',
            ];
        }

        $decoded = json_decode($response, true);

        return $decoded ?: [
            'success' => false,
            'error' => 'Resposta inválida da API matemática.',
        ];
    }
}

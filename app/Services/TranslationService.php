<?php
// app/Services/TranslationService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    private $apiKey;
    private $baseUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    public function translateAirdropContent(array $content, string $targetLanguage): array
    {
        if (!$this->apiKey) {
            throw new \Exception('OpenAI API key not configured');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt($targetLanguage)
                    ],
                    [
                        'role' => 'user',
                        'content' => json_encode($content)
                    ]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.3,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $translatedContent = json_decode($result['choices'][0]['message']['content'], true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $translatedContent;
                }
            }

            throw new \Exception('Translation API request failed: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Translation failed', [
                'error' => $e->getMessage(),
                'content' => $content,
                'language' => $targetLanguage,
            ]);
            
            throw $e;
        }
    }

    private function getSystemPrompt(string $targetLanguage): string
    {
        return "You are a professional translator specializing in cryptocurrency and blockchain content. " .
               "Translate the following airdrop content to {$targetLanguage}. " .
               "Maintain technical accuracy and keep cryptocurrency terms unchanged when appropriate. " .
               "Return only the translated JSON with the same structure as the input. " .
               "Preserve HTML tags if any are present.";
    }

    public function getSupportedLanguages(): array
    {
        return [
            'en' => 'English',
            'sk' => 'Slovak',
            'de' => 'German',
            'es' => 'Spanish',
            'fr' => 'French',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
            'ar' => 'Arabic',
        ];
    }
}

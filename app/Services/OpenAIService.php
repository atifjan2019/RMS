<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openai.com/v1';
    private string $model = 'gpt-4o-mini';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Generate AI reply drafts for a review.
     */
    public function generateReplyDrafts(
        string $reviewerName,
        int $rating,
        ?string $comment,
        string $businessName,
        string $tone = 'professional'
    ): ?string {
        $prompt = $this->buildPrompt($reviewerName, $rating, $comment, $businessName, $tone);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/chat/completions", [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

            if (!$response->successful()) {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return null;
            }

            $data = $response->json();
            $text = $data['choices'][0]['message']['content'] ?? null;

            return $text;
        } catch (\Exception $e) {
            Log::error('OpenAI API exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate all three tone drafts at once.
     */
    public function generateAllDrafts(
        string $reviewerName,
        int $rating,
        ?string $comment,
        string $businessName
    ): array {
        $drafts = [];

        foreach (['friendly', 'professional', 'recovery'] as $tone) {
            $draft = $this->generateReplyDrafts($reviewerName, $rating, $comment, $businessName, $tone);
            $drafts[$tone] = $draft;
        }

        return $drafts;
    }

    /**
     * Build the prompt for AI generation.
     */
    private function buildPrompt(
        string $reviewerName,
        int $rating,
        ?string $comment,
        string $businessName,
        string $tone
    ): string {
        $toneDescription = match ($tone) {
            'friendly' => 'warm, casual, and personable with friendly language',
            'professional' => 'polished, businesslike, and courteous',
            'recovery' => 'empathetic, apologetic, and focused on making things right',
            default => 'professional and courteous',
        };

        $reviewType = match (true) {
            $rating >= 4 => 'positive',
            $rating === 3 => 'neutral',
            default => 'negative',
        };

        $commentSection = $comment
            ? "The customer wrote: \"{$comment}\""
            : "The customer did not leave a written comment.";

        return <<<PROMPT
You are a reputation management assistant for "{$businessName}". Generate a reply to a customer review.

Review Details:
- Reviewer: {$reviewerName}
- Rating: {$rating}/5 stars ({$reviewType} review)
- {$commentSection}

Tone: {$toneDescription}

STRICT SAFETY RULES (MUST FOLLOW):
1. NEVER offer incentives, discounts, or compensation
2. NEVER share private business information or internal details
3. NEVER make admissions of legal liability or fault
4. NEVER include sensitive personal data
5. NEVER promise specific outcomes or timelines
6. Keep the response concise (2-4 sentences)
7. Always thank the customer for their feedback
8. For negative reviews, express genuine concern and invite them to contact the business directly

Generate ONLY the reply text, nothing else. No quotes, no explanation, just the reply:
PROMPT;
    }
}

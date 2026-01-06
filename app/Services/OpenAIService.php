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

    /**
     * Generate business insights summary from all reviews.
     */
    public function generateBusinessSummary(array $reviews, string $businessName, array $stats): ?string
    {
        if (empty($reviews)) {
            return null;
        }

        // Prepare review samples (limit to recent 50 for context length)
        $reviewSamples = array_slice($reviews, 0, 50);
        $reviewTexts = [];
        
        foreach ($reviewSamples as $review) {
            $comment = $review['comment'] ?? 'No comment';
            $reviewTexts[] = "Rating: {$review['rating']}/5 - {$comment}";
        }

        $reviewsText = implode("\n", $reviewTexts);
        $totalReviews = $stats['totalReviews'];
        $avgRating = $stats['avgRating'];
        $ratingDist = json_encode($stats['ratingDistribution']);

        $prompt = <<<PROMPT
You are an AI business analyst. Analyze the following customer reviews for "{$businessName}" and create a comprehensive, insightful summary.

BUSINESS METRICS:
- Total Reviews: {$totalReviews}
- Average Rating: {$avgRating}/5
- Rating Distribution: {$ratingDist}

RECENT REVIEW SAMPLES:
{$reviewsText}

Generate a professional business summary with the following sections:
1. **Overall Sentiment** (1-2 sentences about general customer satisfaction)
2. **Key Strengths** (2-3 bullet points highlighting what customers love most)
3. **Areas for Improvement** (1-2 bullet points on recurring concerns or suggestions)
4. **Recommendation** (1 actionable recommendation to improve ratings)

Keep it concise, data-driven, and actionable. Use markdown formatting. Be specific and reference patterns you see in the reviews.
PROMPT;

        try {
            $response = Http::timeout(45)
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
                    'temperature' => 0.5,
                    'max_tokens' => 800,
                ]);

            if (!$response->successful()) {
                Log::error('OpenAI API error for business summary', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            Log::error('OpenAI API exception for business summary', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Generate AI recommendations for business profile improvements.
     */
    public function generateProfileRecommendations(array $locationData, $tenant): ?string
    {
        $title = $locationData['title'] ?? 'Unknown Business';
        $description = $locationData['profile']['description'] ?? 'No description';
        $categories = json_encode($locationData['categories'] ?? []);
        $phone = $locationData['phoneNumbers']['primaryPhone'] ?? 'Not set';
        $website = $locationData['websiteUri'] ?? 'Not set';
        $address = json_encode($locationData['storefrontAddress'] ?? []);

        $prompt = <<<PROMPT
You are a Google Business Profile optimization expert. Analyze this business profile and provide specific, actionable recommendations to improve visibility, customer engagement, and local SEO.

BUSINESS PROFILE DATA:
- Business Name: {$title}
- Description: {$description}
- Categories: {$categories}
- Phone: {$phone}
- Website: {$website}
- Address: {$address}

Generate a professional analysis with:
1. **Profile Completeness Score** (X/100 with brief explanation)
2. **Critical Issues** (1-3 bullet points of urgent problems to fix)
3. **Optimization Opportunities** (2-3 specific improvements with expected impact)
4. **SEO Keywords** (3-5 recommended keywords/phrases to add to description)

Be specific and actionable. Reference Google's best practices. Use markdown formatting.
PROMPT;

        try {
            $response = Http::timeout(40)
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
                    'temperature' => 0.6,
                    'max_tokens' => 700,
                ]);

            if (!$response->successful()) {
                Log::error('OpenAI API error for profile recommendations', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['choices'][0]['message']['content'] ?? null;
        } catch (\Exception $e) {
            Log::error('OpenAI API exception for profile recommendations', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}

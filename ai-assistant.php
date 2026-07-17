<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/env.php';

load_env_file(__DIR__ . '/.env');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'ok' => false,
        'message' => 'Only POST requests are allowed.',
    ]);
    exit;
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody ?: '{}', true);

if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode([
        'ok' => false,
        'message' => 'Invalid request payload.',
    ]);
    exit;
}

$message = trim((string) ($payload['message'] ?? ''));

if ($message === '') {
    http_response_code(422);
    echo json_encode([
        'ok' => false,
        'message' => 'Please enter a message first.',
    ]);
    exit;
}

function ai_fallback_reply(string $message): string
{
    $normalized = strtolower($message);

    if (
        str_contains($normalized, 'register') ||
        str_contains($normalized, 'registration') ||
        str_contains($normalized, 'sign up') ||
        str_contains($normalized, 'create donor') ||
        str_contains($normalized, 'become donor')
    ) {
        return 'To register as a donor, open the Become a Donor page from the main menu. There you can fill in your blood group, city, availability, and contact preference to create your donor profile.';
    }

    if (
        str_contains($normalized, 'login') ||
        str_contains($normalized, 'log in') ||
        str_contains($normalized, 'dashboard')
    ) {
        return 'Use the Log In button in the top-right corner to open the portal. Donors can sign in there and coordinators can use the same portal for dashboard access.';
    }

    if (
        str_contains($normalized, 'contact') ||
        str_contains($normalized, 'support') ||
        str_contains($normalized, 'help desk')
    ) {
        return 'You can use the Contact page to reach the Blood Saathi support team. The site also shows the emergency helpline at the top for urgent blood requests.';
    }

    if (str_contains($normalized, 'blood group') || str_contains($normalized, 'compatible')) {
        return 'Blood group compatibility should always be confirmed by a hospital, doctor, or blood bank. If you want, I can still explain the common ABO and Rh blood group basics in simple language.';
    }

    if (
        str_contains($normalized, 'eligible') ||
        str_contains($normalized, 'can i donate') ||
        str_contains($normalized, 'who can donate')
    ) {
        return 'In general, healthy adults may be able to donate blood if they meet age, weight, and medical screening requirements. Final eligibility depends on local blood bank rules and staff screening on the day of donation.';
    }

    if (
        str_contains($normalized, 'emergency') ||
        str_contains($normalized, 'urgent') ||
        str_contains($normalized, 'need blood')
    ) {
        return 'For an urgent blood need, please contact the hospital or blood bank immediately and use the Emergency Help or Find Donor pages to submit the required details. I can help you prepare the blood group, city, hospital name, and urgency level before you submit.';
    }

    if (
        str_contains($normalized, 'find donor') ||
        str_contains($normalized, 'request blood') ||
        str_contains($normalized, 'donor')
    ) {
        return 'You can use Find Donor / Request Blood to search by city, blood group, and availability. If you are ready to help regularly, the Become a Donor page lets you create a donor profile.';
    }

    return 'I can help with blood donation basics, donor eligibility guidance, registration steps, blood request steps, and finding the right page on this site. Try asking things like "How do I become a donor?" or "Where can I request blood?"';
}

$geminiApiKey = trim((string) getenv('GEMINI_API_KEY'));
$geminiModel = trim((string) getenv('GEMINI_MODEL'));
$openAiApiKey = trim((string) getenv('OPENAI_API_KEY'));
$openAiModel = trim((string) getenv('OPENAI_MODEL'));

if ($geminiApiKey === '' && $openAiApiKey !== '' && str_starts_with($openAiApiKey, 'AIza')) {
    $geminiApiKey = $openAiApiKey;
}

if ($geminiModel === '') {
    $geminiModel = 'gemini-1.5-flash';
}

if ($openAiModel === '') {
    $openAiModel = 'gpt-4.1-mini';
}

$systemPrompt = <<<PROMPT
You are Blood Saathi Assistant for a blood donation website.

Your role:
- Help users understand blood donation basics in simple language.
- Help users navigate the site pages such as Find Donor, Become a Donor, FAQ, Emergency Help, and Contact.
- Help users prepare information for donor registration or urgent blood requests.
- Answer briefly, clearly, and calmly.

Safety rules:
- Do not provide a final medical diagnosis.
- Do not confirm transfusion compatibility as a medical decision.
- Do not replace hospitals, blood banks, or licensed clinicians.
- If the user describes an emergency, tell them to contact the hospital, blood bank, or emergency services immediately.
- When discussing eligibility, say screening rules may vary and final eligibility is decided by medical staff.

Tone:
- Warm, practical, reassuring, and concise.
- Prefer short paragraphs over long lists.
PROMPT;

function ai_send_request(string $url, array $headers, array $payload): array
{
    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
        CURLOPT_TIMEOUT => 25,
    ]);

    $responseBody = curl_exec($ch);
    $curlError = curl_error($ch);
    $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    curl_close($ch);

    return [$responseBody, $curlError, $statusCode];
}

if ($geminiApiKey !== '') {
    $requestPayload = [
        'systemInstruction' => [
            'parts' => [
                [
                    'text' => $systemPrompt,
                ],
            ],
        ],
        'contents' => [
            [
                'role' => 'user',
                'parts' => [
                    [
                        'text' => $message,
                    ],
                ],
            ],
        ],
        'generationConfig' => [
            'maxOutputTokens' => 300,
            'temperature' => 0.5,
        ],
    ];

    [$responseBody, $curlError, $statusCode] = ai_send_request(
        'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($geminiModel) . ':generateContent?key=' . rawurlencode($geminiApiKey),
        [
            'Content-Type: application/json',
        ],
        $requestPayload
    );

    if ($responseBody === false || $curlError !== '') {
        echo json_encode([
            'ok' => true,
            'message' => ai_fallback_reply($message),
            'source' => 'fallback',
        ]);
        exit;
    }

    $responseData = json_decode($responseBody, true);

    if (!is_array($responseData) || $statusCode >= 400) {
        echo json_encode([
            'ok' => true,
            'message' => ai_fallback_reply($message),
            'source' => 'fallback',
        ]);
        exit;
    }

    $assistantText = '';

    foreach (($responseData['candidates'] ?? []) as $candidate) {
        foreach (($candidate['content']['parts'] ?? []) as $part) {
            if (isset($part['text']) && is_string($part['text'])) {
                $assistantText .= $part['text'];
            }
        }
    }

    $assistantText = trim($assistantText);

    if ($assistantText === '') {
        $assistantText = 'I could not generate a reply just now. Please try asking again.';
    }

    echo json_encode([
        'ok' => true,
        'message' => $assistantText,
        'source' => 'gemini',
    ]);
    exit;
}

if ($openAiApiKey !== '') {
    $requestPayload = [
        'model' => $openAiModel,
        'input' => [
            [
                'role' => 'system',
                'content' => [
                    [
                        'type' => 'input_text',
                        'text' => $systemPrompt,
                    ],
                ],
            ],
            [
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'input_text',
                        'text' => $message,
                    ],
                ],
            ],
        ],
        'max_output_tokens' => 300,
    ];

    [$responseBody, $curlError, $statusCode] = ai_send_request(
        'https://api.openai.com/v1/responses',
        [
            'Authorization: Bearer ' . $openAiApiKey,
            'Content-Type: application/json',
        ],
        $requestPayload
    );

    if ($responseBody === false || $curlError !== '') {
        echo json_encode([
            'ok' => true,
            'message' => ai_fallback_reply($message),
            'source' => 'fallback',
        ]);
        exit;
    }

    $responseData = json_decode($responseBody, true);

    if (!is_array($responseData) || $statusCode >= 400) {
        echo json_encode([
            'ok' => true,
            'message' => ai_fallback_reply($message),
            'source' => 'fallback',
        ]);
        exit;
    }

    $assistantText = trim((string) ($responseData['output_text'] ?? ''));

    if ($assistantText === '' && isset($responseData['output']) && is_array($responseData['output'])) {
        foreach ($responseData['output'] as $item) {
            if (!is_array($item) || ($item['type'] ?? '') !== 'message') {
                continue;
            }

            foreach (($item['content'] ?? []) as $contentItem) {
                if (!is_array($contentItem) || ($contentItem['type'] ?? '') !== 'output_text') {
                    continue;
                }

                $assistantText .= (string) ($contentItem['text'] ?? '');
            }
        }
    }

    if ($assistantText === '') {
        $assistantText = 'I could not generate a reply just now. Please try asking again.';
    }

    echo json_encode([
        'ok' => true,
        'message' => $assistantText,
        'source' => 'openai',
    ]);
    exit;
}

echo json_encode([
    'ok' => true,
    'message' => ai_fallback_reply($message),
    'source' => 'fallback',
]);

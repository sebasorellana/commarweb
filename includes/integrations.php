<?php
require_once __DIR__ . '/settings.php';

if (!function_exists('commar_clean_integration_id')) {
    function commar_clean_integration_id(string $value, string $pattern): string
    {
        $value = trim($value);

        return preg_match($pattern, $value) ? $value : '';
    }
}

if (!function_exists('commar_google_tag_manager_id')) {
    function commar_google_tag_manager_id(): string
    {
        return commar_clean_integration_id((string) commar_setting('google_tag_manager_id'), '/^GTM-[A-Z0-9_-]+$/i');
    }
}

if (!function_exists('commar_google_analytics_id')) {
    function commar_google_analytics_id(): string
    {
        return commar_clean_integration_id((string) commar_setting('google_analytics_id'), '/^G-[A-Z0-9]+$/i');
    }
}

if (!function_exists('commar_recaptcha_enabled')) {
    function commar_recaptcha_enabled(): bool
    {
        return (string) commar_setting('recaptcha_enabled') === '1'
            && commar_recaptcha_site_key() !== ''
            && commar_recaptcha_secret_key() !== '';
    }
}

if (!function_exists('commar_recaptcha_version')) {
    function commar_recaptcha_version(): string
    {
        return (string) commar_setting('recaptcha_version') === 'v2' ? 'v2' : 'v3';
    }
}

if (!function_exists('commar_recaptcha_site_key')) {
    function commar_recaptcha_site_key(): string
    {
        return trim((string) commar_setting('recaptcha_site_key'));
    }
}

if (!function_exists('commar_recaptcha_secret_key')) {
    function commar_recaptcha_secret_key(): string
    {
        return trim((string) commar_setting('recaptcha_secret_key'));
    }
}

if (!function_exists('commar_recaptcha_v3_score')) {
    function commar_recaptcha_v3_score(): float
    {
        return max(0.0, min(1.0, (float) commar_setting('recaptcha_v3_score')));
    }
}

if (!function_exists('commar_recaptcha_form_attributes')) {
    function commar_recaptcha_form_attributes(string $action): string
    {
        if (!commar_recaptcha_enabled() || commar_recaptcha_version() !== 'v3') {
            return '';
        }

        return ' data-recaptcha-form data-recaptcha-action="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '"';
    }
}

if (!function_exists('commar_recaptcha_field')) {
    function commar_recaptcha_field(string $action): string
    {
        if (!commar_recaptcha_enabled()) {
            return '';
        }

        if (commar_recaptcha_version() === 'v2') {
            return '<div class="commar-recaptcha g-recaptcha" data-sitekey="' . htmlspecialchars(commar_recaptcha_site_key(), ENT_QUOTES, 'UTF-8') . '"></div>';
        }

        return '<input type="hidden" name="g-recaptcha-response" value="" data-recaptcha-token><input type="hidden" name="recaptcha_action" value="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('commar_recaptcha_verify')) {
    function commar_recaptcha_verify(string $expectedAction = ''): bool
    {
        if (!commar_recaptcha_enabled()) {
            return true;
        }

        $token = trim((string) ($_POST['g-recaptcha-response'] ?? ''));
        if ($token === '') {
            return false;
        }

        $payload = http_build_query([
            'secret' => commar_recaptcha_secret_key(),
            'response' => $token,
            'remoteip' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
        ]);
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 8,
            ],
        ]);
        $response = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if ($response === false && function_exists('curl_init')) {
            $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
            if ($curl !== false) {
                curl_setopt_array($curl, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $payload,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 8,
                    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
                ]);
                $curlResponse = curl_exec($curl);
                curl_close($curl);

                if (is_string($curlResponse)) {
                    $response = $curlResponse;
                }
            }
        }

        if ($response === false) {
            return false;
        }

        $result = json_decode($response, true);
        if (!is_array($result) || empty($result['success'])) {
            return false;
        }

        if (commar_recaptcha_version() === 'v3') {
            $score = (float) ($result['score'] ?? 0);
            $action = (string) ($result['action'] ?? '');

            if ($score < commar_recaptcha_v3_score()) {
                return false;
            }

            if ($expectedAction !== '' && $action !== '' && $action !== $expectedAction) {
                return false;
            }
        }

        return true;
    }
}

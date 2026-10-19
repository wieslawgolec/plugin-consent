<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Helper;

/**
 * Signed tokens for public preference-page links (contact id + expiry).
 *
 * Uses HMAC-SHA256. Secret comes from plugin/Mautic secret in production.
 */
final class ConsentToken
{
    public function __construct(
        private readonly string $secret,
        private readonly int $ttlSeconds = 2_592_000, // 30 days
    ) {
    }

    public function create(int $contactId, ?int $expiresAt = null): string
    {
        $exp = $expiresAt ?? (time() + $this->ttlSeconds);
        $payload = $contactId . '|' . $exp;
        $sig = hash_hmac('sha256', $payload, $this->secret);

        return rtrim(strtr(base64_encode($payload . '|' . $sig), '+/', '-_'), '=');
    }

    /**
     * @return array{valid: bool, contact_id?: int, error?: string}
     */
    public function validate(string $token): array
    {
        $raw = base64_decode(strtr($token, '-_', '+/'), true);
        if (false === $raw) {
            return ['valid' => false, 'error' => 'malformed'];
        }

        $parts = explode('|', $raw);
        if (3 !== count($parts)) {
            return ['valid' => false, 'error' => 'malformed'];
        }

        [$contactId, $exp, $sig] = $parts;
        $payload = $contactId . '|' . $exp;
        $expected = hash_hmac('sha256', $payload, $this->secret);

        if (!hash_equals($expected, $sig)) {
            return ['valid' => false, 'error' => 'signature'];
        }

        if ((int) $exp < time()) {
            return ['valid' => false, 'error' => 'expired'];
        }

        return ['valid' => true, 'contact_id' => (int) $contactId];
    }
}

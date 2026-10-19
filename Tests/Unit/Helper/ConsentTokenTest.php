<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Helper;

use MauticPlugin\MauticConsentBundle\Helper\ConsentToken;
use PHPUnit\Framework\TestCase;

final class ConsentTokenTest extends TestCase
{
    public function testRoundTrip(): void
    {
        $helper = new ConsentToken('test-secret-key');
        $token  = $helper->create(12345);
        $parsed = $helper->validate($token);

        self::assertTrue($parsed['valid']);
        self::assertSame(12345, $parsed['contact_id']);
    }

    public function testTamperedFails(): void
    {
        $helper = new ConsentToken('secret');
        $token  = $helper->create(1);
        $bad    = $token . 'x';

        self::assertFalse($helper->validate($bad)['valid']);
    }

    public function testWrongSecretFails(): void
    {
        $token = (new ConsentToken('a'))->create(1);
        self::assertFalse((new ConsentToken('b'))->validate($token)['valid']);
    }

    public function testExpiredFails(): void
    {
        $helper = new ConsentToken('secret', 1);
        $token  = $helper->create(9, time() - 10);
        $parsed = $helper->validate($token);

        self::assertFalse($parsed['valid']);
        self::assertSame('expired', $parsed['error']);
    }
}

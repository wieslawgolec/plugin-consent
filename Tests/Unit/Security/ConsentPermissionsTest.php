<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Security;

use MauticPlugin\MauticConsentBundle\Security\Permissions\ConsentPermissions;
use PHPUnit\Framework\TestCase;

final class ConsentPermissionsTest extends TestCase
{
    public function testDefinitions(): void
    {
        $defs = ConsentPermissions::getPermissionDefinitions();
        self::assertArrayHasKey('purposes', $defs);
        self::assertArrayHasKey('records', $defs);
        self::assertContains('view', $defs['purposes']);
    }

    public function testIsValid(): void
    {
        self::assertTrue(ConsentPermissions::isValid(ConsentPermissions::PURPOSES_VIEW));
        self::assertFalse(ConsentPermissions::isValid('consent:foo:bar'));
    }
}

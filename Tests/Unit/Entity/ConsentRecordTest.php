<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Entity;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use PHPUnit\Framework\TestCase;

final class ConsentRecordTest extends TestCase
{
    public function testGrantAndWithdraw(): void
    {
        $r = new ConsentRecord();
        $r->setContactId(42)->setPurposeId(7)->setPurposeVersion(1)->setSource(ConsentRecord::SOURCE_FORM);

        $r->grant();
        self::assertTrue($r->isGranted());
        self::assertFalse($r->isWithdrawn());
        self::assertNotNull($r->getGrantedAt());
        self::assertNull($r->getWithdrawnAt());

        $r->withdraw();
        self::assertTrue($r->isWithdrawn());
        self::assertFalse($r->isGranted());
        self::assertNotNull($r->getWithdrawnAt());
    }

    public function testInvalidStatusThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new ConsentRecord())->setStatus('nope');
    }

    public function testNeedsReconsent(): void
    {
        $r = new ConsentRecord();
        $r->setPurposeVersion(1)->grant();
        self::assertTrue($r->needsReconsent(2));
        self::assertFalse($r->needsReconsent(1));

        $r->withdraw();
        self::assertFalse($r->needsReconsent(2));
    }
}

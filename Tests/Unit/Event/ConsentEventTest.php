<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Event;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Event\ConsentEvent;
use PHPUnit\Framework\TestCase;

final class ConsentEventTest extends TestCase
{
    public function testAccessors(): void
    {
        $r = new ConsentRecord();
        $r->setContactId(5)->grant();
        $p = new Purpose();
        $p->setName('x');

        $e = new ConsentEvent($r, $p);
        self::assertSame(5, $e->getContactId());
        self::assertTrue($e->isGranted());
        self::assertSame($p, $e->getPurpose());
        self::assertSame($r, $e->getRecord());
    }
}

<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Tests\Unit\Entity;

use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use PHPUnit\Framework\TestCase;

final class PurposeTest extends TestCase
{
    public function testDefaultsAndSetters(): void
    {
        $p = new Purpose();
        $p->setName('Marketing email')
            ->setAlias('Marketing Email!')
            ->setDescription('News and offers')
            ->setLegalBasis('consent')
            ->setPolicyText('We will email you offers.')
            ->setVersion(2)
            ->setRequired(true)
            ->setOrder(10)
            ->setChannelHint('email')
            ->setIsPublished(true);

        self::assertSame('Marketing email', $p->getName());
        self::assertSame('marketing-email-', $p->getAlias());
        self::assertSame(2, $p->getVersion());
        self::assertTrue($p->isRequired());
        self::assertTrue($p->canBePresented());
        self::assertSame('email', $p->getChannelHint());
    }

    public function testBumpVersion(): void
    {
        $p = new Purpose();
        $p->setVersion(1);
        $p->bumpVersion();
        self::assertSame(2, $p->getVersion());
    }

    public function testUnpublishedCannotBePresented(): void
    {
        $p = new Purpose();
        $p->setIsPublished(false);
        self::assertFalse($p->canBePresented());
    }
}

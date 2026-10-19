<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Helper;

/**
 * Describes segment filter definitions for "has consent for purpose".
 *
 * In full Mautic this is registered via SegmentFilters or LeadListSubscriber.
 */
final class SegmentConsentFilter
{
    public const FILTER_HAS_CONSENT = 'consent_has_purpose';

    /**
     * @return list<array{alias: string, label: string, type: string, operators: list<string>}>
     */
    public static function getDefinitions(): array
    {
        return [
            [
                'alias'     => self::FILTER_HAS_CONSENT,
                'label'     => 'Has consent for purpose',
                'type'      => 'text',
                'operators' => ['=', '!='],
            ],
        ];
    }

    /**
     * Evaluate filter against ledger (unit-testable pure logic).
     */
    public static function matches(
        ConsentLedger $ledger,
        int $contactId,
        string $purposeAlias,
        string $operator = '=',
    ): bool {
        $has = $ledger->hasConsent($contactId, $purposeAlias);

        return '=' === $operator ? $has : !$has;
    }
}

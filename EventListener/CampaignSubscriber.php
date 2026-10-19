<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\EventListener;

use MauticPlugin\MauticConsentBundle\Helper\ConsentLedger;
use MauticPlugin\MauticConsentBundle\Helper\SegmentConsentFilter;

/**
 * Campaign decision: contact has (or does not have) consent for a purpose alias.
 *
 * In full Mautic this registers a campaign condition via CampaignEvents.
 * Unit tests call evaluate() directly.
 */
final class CampaignSubscriber
{
    public const CONDITION_HAS_CONSENT = 'consent.has_purpose';

    public function __construct(
        private readonly ConsentLedger $ledger,
    ) {
    }

    /**
     * @param array{purpose_alias?: string, operator?: string} $properties
     */
    public function evaluate(int $contactId, array $properties): bool
    {
        $alias    = (string) ($properties['purpose_alias'] ?? '');
        $operator = (string) ($properties['operator'] ?? '=');

        if ('' === $alias || $contactId < 1) {
            return false;
        }

        return SegmentConsentFilter::matches($this->ledger, $contactId, $alias, $operator);
    }

    /**
     * Campaign builder metadata.
     *
     * @return array{key: string, label: string, formFields: list<array>}
     */
    public static function getConditionDefinition(): array
    {
        return [
            'key'   => self::CONDITION_HAS_CONSENT,
            'label' => 'Contact has consent for purpose',
            'formFields' => [
                ['name' => 'purpose_alias', 'label' => 'Purpose alias', 'type' => 'text', 'required' => true],
                [
                    'name'    => 'operator',
                    'label'   => 'Operator',
                    'type'    => 'choice',
                    'options' => ['=' => 'Has consent', '!=' => 'Does not have consent'],
                ],
            ],
        ];
    }
}

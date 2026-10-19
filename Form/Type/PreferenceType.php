<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Form\Type;

use MauticPlugin\MauticConsentBundle\Entity\Purpose;

/**
 * Builds preference-page field map from published purposes.
 */
final class PreferenceType
{
    public const NAME = 'consent_preference';

    /**
     * @param list<Purpose> $purposes
     * @return list<array{name: string, label: string, type: string, required: bool, alias: string}>
     */
    public static function fieldsFromPurposes(array $purposes): array
    {
        $fields = [];
        foreach ($purposes as $purpose) {
            if (!$purpose instanceof Purpose || !$purpose->canBePresented()) {
                continue;
            }
            $fields[] = [
                'name'     => 'purpose_' . $purpose->getAlias(),
                'label'    => $purpose->getName(),
                'type'     => 'checkbox',
                'required' => $purpose->isRequired(),
                'alias'    => $purpose->getAlias(),
            ];
        }

        return $fields;
    }

    /**
     * Map submitted form data (purpose_alias => on/off) to alias => bool.
     *
     * @param array<string, mixed> $submitted
     * @param list<Purpose>        $purposes
     * @return array<string, bool>
     */
    public static function choicesFromSubmission(array $submitted, array $purposes): array
    {
        $choices = [];
        foreach ($purposes as $purpose) {
            if (!$purpose instanceof Purpose) {
                continue;
            }
            $key = 'purpose_' . $purpose->getAlias();
            $choices[$purpose->getAlias()] = !empty($submitted[$key]);
        }

        return $choices;
    }
}

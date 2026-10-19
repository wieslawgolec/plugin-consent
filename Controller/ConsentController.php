<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Controller;

use MauticPlugin\MauticConsentBundle\Entity\ConsentRecord;
use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Model\ConsentModel;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;

/**
 * Admin UI for consent records: list, contact detail, manual grant/withdraw.
 */
final class ConsentController
{
    public function __construct(
        private readonly ConsentModel $consentModel,
        private readonly PurposeModel $purposeModel,
    ) {
    }

    /**
     * @return array{template: string, vars: array}
     */
    public function indexAction(?int $contactId = null): array
    {
        $items = null !== $contactId
            ? $this->consentModel->getForContact($contactId)
            : $this->consentModel->getRepository()->getEntities();

        $purposeMap = [];
        foreach ($this->purposeModel->getRepository()->getEntities() as $p) {
            if ($p instanceof Purpose && null !== $p->getId()) {
                $purposeMap[$p->getId()] = $p;
            }
        }

        return [
            'template' => 'Consent/list.html.twig',
            'vars'     => [
                'items'      => $items,
                'purposes'   => $purposeMap,
                'contact_id' => $contactId,
            ],
        ];
    }

    /**
     * Contact-centric timeline of consent decisions.
     *
     * @return array{template: string, vars: array}
     */
    public function contactAction(int $contactId): array
    {
        return $this->indexAction($contactId);
    }

    /**
     * Manual grant from admin UI.
     *
     * @param array<string, mixed> $data
     * @return array{template: string, vars: array, record?: ConsentRecord, error?: string}
     */
    public function grantAction(array $data): array
    {
        $contactId = (int) ($data['contact_id'] ?? 0);
        $alias     = (string) ($data['purpose_alias'] ?? '');
        $purpose   = '' !== $alias ? $this->purposeModel->getByAlias($alias) : null;

        if ($contactId < 1 || null === $purpose) {
            return [
                'template' => 'Consent/list.html.twig',
                'vars'     => [
                    'items' => [],
                    'error' => 'invalid_contact_or_purpose',
                ],
                'error' => 'invalid_contact_or_purpose',
            ];
        }

        $record = $this->consentModel->grantForContact(
            $contactId,
            $purpose,
            ConsentRecord::SOURCE_ADMIN,
            [
                'notes'         => isset($data['notes']) ? (string) $data['notes'] : null,
                'source_detail' => 'admin_ui',
            ]
        );

        $result = $this->contactAction($contactId);
        $result['record'] = $record;

        return $result;
    }

    /**
     * Manual withdraw from admin UI.
     *
     * @param array<string, mixed> $data
     * @return array{template: string, vars: array, record?: ConsentRecord, error?: string}
     */
    public function withdrawAction(array $data): array
    {
        $contactId = (int) ($data['contact_id'] ?? 0);
        $alias     = (string) ($data['purpose_alias'] ?? '');
        $purpose   = '' !== $alias ? $this->purposeModel->getByAlias($alias) : null;

        if ($contactId < 1 || null === $purpose) {
            return [
                'template' => 'Consent/list.html.twig',
                'vars'     => ['items' => [], 'error' => 'invalid_contact_or_purpose'],
                'error'    => 'invalid_contact_or_purpose',
            ];
        }

        $record = $this->consentModel->withdrawForContact(
            $contactId,
            $purpose,
            ConsentRecord::SOURCE_ADMIN,
            [
                'notes'         => isset($data['notes']) ? (string) $data['notes'] : null,
                'source_detail' => 'admin_ui',
            ]
        );

        $result = $this->contactAction($contactId);
        $result['record'] = $record;

        return $result;
    }

    /**
     * Admin form to grant/withdraw (field definitions).
     *
     * @return array{template: string, vars: array}
     */
    public function manageAction(?int $contactId = null): array
    {
        return [
            'template' => 'Consent/manage.html.twig',
            'vars'     => [
                'contact_id' => $contactId,
                'purposes'   => $this->purposeModel->getPublished(),
            ],
        ];
    }
}

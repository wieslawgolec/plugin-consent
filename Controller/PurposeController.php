<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Controller;

use MauticPlugin\MauticConsentBundle\Entity\Purpose;
use MauticPlugin\MauticConsentBundle\Form\Type\PurposeType;
use MauticPlugin\MauticConsentBundle\Model\PurposeModel;

/**
 * Admin CRUD for purposes. Renders Twig views under Resources/views/Purpose/.
 */
final class PurposeController
{
    public function __construct(
        private readonly PurposeModel $model,
    ) {
    }

    /**
     * @return array{template: string, vars: array}
     */
    public function indexAction(): array
    {
        return [
            'template' => 'Purpose/list.html.twig',
            'vars'     => [
                'items' => $this->model->getRepository()->getEntities(),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{template: string, vars: array, entity?: Purpose}
     */
    public function newAction(array $data = []): array
    {
        $entity = new Purpose();
        if ([] !== $data) {
            $this->hydrate($entity, $data);
            $this->model->saveEntity($entity);
        }

        return [
            'template' => 'Purpose/form.html.twig',
            'vars'     => [
                'entity' => $entity,
                'fields' => PurposeType::getFields(),
            ],
            'entity' => $entity,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array{template: string, vars: array, entity?: Purpose|null}
     */
    public function editAction(int $id, array $data = []): array
    {
        $entity = $this->model->getEntity($id);
        if (null === $entity) {
            return [
                'template' => 'Purpose/list.html.twig',
                'vars'     => ['items' => [], 'error' => 'not_found'],
            ];
        }
        if ([] !== $data) {
            $this->hydrate($entity, $data);
            $this->model->saveEntity($entity);
        }

        return [
            'template' => 'Purpose/form.html.twig',
            'vars'     => [
                'entity' => $entity,
                'fields' => PurposeType::getFields(),
            ],
            'entity' => $entity,
        ];
    }

    public function deleteAction(int $id): array
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            $this->model->deleteEntity($entity);
        }

        return $this->indexAction();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hydrate(Purpose $entity, array $data): void
    {
        if (isset($data['name'])) {
            $entity->setName((string) $data['name']);
        }
        if (isset($data['alias'])) {
            $entity->setAlias((string) $data['alias']);
        } elseif (isset($data['name'])) {
            $entity->setAlias((string) $data['name']);
        }
        if (array_key_exists('description', $data)) {
            $entity->setDescription(null !== $data['description'] ? (string) $data['description'] : null);
        }
        if (isset($data['legalBasis'])) {
            $entity->setLegalBasis((string) $data['legalBasis']);
        }
        if (array_key_exists('policyText', $data)) {
            $entity->setPolicyText(null !== $data['policyText'] ? (string) $data['policyText'] : null);
        }
        if (isset($data['version'])) {
            $entity->setVersion((int) $data['version']);
        }
        if (isset($data['required'])) {
            $entity->setRequired((bool) $data['required']);
        }
        if (isset($data['order'])) {
            $entity->setOrder((int) $data['order']);
        }
        if (array_key_exists('channelHint', $data)) {
            $hint = $data['channelHint'];
            $entity->setChannelHint('' === $hint || null === $hint ? null : (string) $hint);
        }
        if (isset($data['isPublished'])) {
            $entity->setIsPublished((bool) $data['isPublished']);
        }
    }
}

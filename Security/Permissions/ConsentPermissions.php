<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Security\Permissions;

/**
 * Permission keys for consent admin UI.
 *
 * Mirrors plugin-social style: standard CRUD on purposes and consent records.
 * In full Mautic this extends AbstractPermissions.
 */
class ConsentPermissions
{
    public const BUNDLE = 'consent';

    public const PURPOSES_VIEW   = 'consent:purposes:view';
    public const PURPOSES_EDIT   = 'consent:purposes:edit';
    public const PURPOSES_CREATE = 'consent:purposes:create';
    public const PURPOSES_DELETE = 'consent:purposes:delete';
    public const PURPOSES_FULL   = 'consent:purposes:full';

    public const RECORDS_VIEW   = 'consent:records:view';
    public const RECORDS_EDIT   = 'consent:records:edit';
    public const RECORDS_CREATE = 'consent:records:create';
    public const RECORDS_DELETE = 'consent:records:delete';
    public const RECORDS_FULL   = 'consent:records:full';

    /**
     * @return array<string, list<string>>
     */
    public static function getPermissionDefinitions(): array
    {
        return [
            'purposes' => ['view', 'edit', 'create', 'delete', 'full'],
            'records'  => ['view', 'edit', 'create', 'delete', 'full'],
        ];
    }

    public static function isValid(string $permission): bool
    {
        $all = [
            self::PURPOSES_VIEW, self::PURPOSES_EDIT, self::PURPOSES_CREATE,
            self::PURPOSES_DELETE, self::PURPOSES_FULL,
            self::RECORDS_VIEW, self::RECORDS_EDIT, self::RECORDS_CREATE,
            self::RECORDS_DELETE, self::RECORDS_FULL,
        ];

        return in_array($permission, $all, true);
    }
}

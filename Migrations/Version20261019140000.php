<?php

declare(strict_types=1);

namespace MauticPlugin\MauticConsentBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates consent_purposes and consent_records tables.
 */
final class Version20261019140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Consent ledger: purposes + consent records';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable('consent_purposes')) {
            $table = $schema->createTable('consent_purposes');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
            $table->addColumn('name', 'string', ['length' => 191]);
            $table->addColumn('alias', 'string', ['length' => 191]);
            $table->addColumn('description', 'text', ['notnull' => false]);
            $table->addColumn('legal_basis', 'string', ['length' => 64, 'default' => 'consent']);
            $table->addColumn('policy_text', 'text', ['notnull' => false]);
            $table->addColumn('version', 'integer', ['default' => 1]);
            $table->addColumn('required', 'boolean', ['default' => false]);
            $table->addColumn('purpose_order', 'integer', ['default' => 0]);
            $table->addColumn('channel_hint', 'string', ['length' => 64, 'notnull' => false]);
            $table->addColumn('is_published', 'boolean', ['default' => true]);
            $table->addColumn('date_added', 'datetime', ['notnull' => false]);
            $table->addColumn('date_modified', 'datetime', ['notnull' => false]);
            $table->addColumn('created_by', 'integer', ['notnull' => false]);
            $table->addColumn('created_by_user', 'string', ['length' => 191, 'notnull' => false]);
            $table->addColumn('modified_by', 'integer', ['notnull' => false]);
            $table->addColumn('modified_by_user', 'string', ['length' => 191, 'notnull' => false]);
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['alias'], 'consent_purpose_alias_uniq');
            $table->addIndex(['is_published'], 'consent_purpose_published');
        }

        if (!$schema->hasTable('consent_records')) {
            $table = $schema->createTable('consent_records');
            $table->addColumn('id', 'integer', ['autoincrement' => true, 'unsigned' => true]);
            $table->addColumn('contact_id', 'integer', ['unsigned' => true]);
            $table->addColumn('purpose_id', 'integer', ['unsigned' => true]);
            $table->addColumn('status', 'string', ['length' => 32]);
            $table->addColumn('purpose_version', 'integer', ['default' => 1]);
            $table->addColumn('source', 'string', ['length' => 64]);
            $table->addColumn('source_detail', 'string', ['length' => 255, 'notnull' => false]);
            $table->addColumn('ip_address', 'string', ['length' => 64, 'notnull' => false]);
            $table->addColumn('user_agent', 'string', ['length' => 255, 'notnull' => false]);
            $table->addColumn('granted_at', 'datetime', ['notnull' => false]);
            $table->addColumn('withdrawn_at', 'datetime', ['notnull' => false]);
            $table->addColumn('date_added', 'datetime', ['notnull' => false]);
            $table->addColumn('date_modified', 'datetime', ['notnull' => false]);
            $table->addColumn('notes', 'text', ['notnull' => false]);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['contact_id', 'purpose_id'], 'consent_contact_purpose');
            $table->addIndex(['status'], 'consent_status');
            $table->addIndex(['contact_id', 'status'], 'consent_contact_status');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable('consent_records')) {
            $schema->dropTable('consent_records');
        }
        if ($schema->hasTable('consent_purposes')) {
            $schema->dropTable('consent_purposes');
        }
    }
}

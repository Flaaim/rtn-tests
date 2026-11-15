<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261115224751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds pg_trgm extension and GIN index for event payload search optimization';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
        $this->addSql('CREATE INDEX CONCURRENTLY idx_event_store_payload_trgm ON event_store_events USING GIN (payload gin_trgm_ops);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX CONCURRENTLY IF EXISTS idx_event_store_payload_trgm;');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}

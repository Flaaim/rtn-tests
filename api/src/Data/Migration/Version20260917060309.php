<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260917060309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payments (created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, confirmed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, id VARCHAR NOT NULL, external_id VARCHAR(64) NOT NULL, user_id VARCHAR(255) NOT NULL, plan VARCHAR(16) NOT NULL, status VARCHAR(16) NOT NULL, duration_days INT NOT NULL, amount VARCHAR(16) NOT NULL, currency VARCHAR(3) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_65D29B329F75D7B0 ON payments (external_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE payments');
    }
}

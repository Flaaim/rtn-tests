<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260818055410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tests ADD number_of_tickets INT NOT NULL');
        $this->addSql('ALTER TABLE tests ADD number_questions_in_ticket INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1260FC5E989D9B62 ON tests (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1260FC5E989D9B62');
        $this->addSql('ALTER TABLE tests DROP number_of_tickets');
        $this->addSql('ALTER TABLE tests DROP number_questions_in_ticket');
    }
}

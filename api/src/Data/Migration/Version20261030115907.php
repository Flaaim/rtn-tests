<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261030115907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses ADD file_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE courses RENAME COLUMN render_file TO file_file_path');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses ADD render_file VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE courses DROP file_file_path');
        $this->addSql('ALTER TABLE courses DROP file_name');
    }
}

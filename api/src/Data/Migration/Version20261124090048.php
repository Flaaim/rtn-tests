<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261124090048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add name column to products and populate it from courses';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products ADD name VARCHAR(255) DEFAULT NULL');
        $this->addSql('
            UPDATE products p
            SET name = c.name
            FROM courses c
            WHERE p.course_id = c.course_id
        ');

        $this->addSql('ALTER TABLE products ALTER COLUMN name SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP name');
    }
}

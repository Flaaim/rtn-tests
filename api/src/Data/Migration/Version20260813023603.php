<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260813023603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tests (status VARCHAR NOT NULL, id VARCHAR NOT NULL, name VARCHAR(255) NOT NULL, cipher VARCHAR(255) NOT NULL, description VARCHAR(512) NOT NULL, allowed_mistakes INT NOT NULL, course_ids JSONB NOT NULL, tickets JSONB NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT fk_8adc54d5591cc992');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5591CC992 FOREIGN KEY (course_id) REFERENCES courses (course_id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tests');
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D5591CC992');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT fk_8adc54d5591cc992 FOREIGN KEY (course_id) REFERENCES courses (course_id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

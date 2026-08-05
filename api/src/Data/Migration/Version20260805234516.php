<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260805234516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE courses (status VARCHAR NOT NULL, course_id VARCHAR NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (course_id))');
        $this->addSql('CREATE TABLE questions (id VARCHAR(255) NOT NULL, text VARCHAR(512) NOT NULL, question_img VARCHAR(255) NOT NULL, answers JSONB NOT NULL, course_id VARCHAR NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_8ADC54D5591CC992 ON questions (course_id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5591CC992 FOREIGN KEY (course_id) REFERENCES courses (course_id) ON DELETE RESTRICT NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE questions DROP CONSTRAINT FK_8ADC54D5591CC992');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE questions');
    }
}

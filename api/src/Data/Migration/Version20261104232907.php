<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261104232907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_recipients ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE product_recipients ADD failed_reason VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_recipients ALTER send_at DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_recipients DROP created_at');
        $this->addSql('ALTER TABLE product_recipients DROP failed_reason');
        $this->addSql('ALTER TABLE product_recipients ALTER send_at SET NOT NULL');
    }
}

<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105085802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_payments DROP CONSTRAINT product_payments_pkey');
        $this->addSql('ALTER TABLE product_payments RENAME COLUMN id TO payment_id');
        $this->addSql('ALTER TABLE product_payments ADD PRIMARY KEY (payment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_payments DROP CONSTRAINT product_payments_pkey');
        $this->addSql('ALTER TABLE product_payments RENAME COLUMN payment_id TO id');
        $this->addSql('ALTER TABLE product_payments ADD PRIMARY KEY (id)');
    }
}

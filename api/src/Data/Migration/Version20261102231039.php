<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261102231039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products RENAME COLUMN amount_amount TO amount');
        $this->addSql('ALTER TABLE products RENAME COLUMN amount_currency TO currency');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products RENAME COLUMN amount TO amount_amount');
        $this->addSql('ALTER TABLE products RENAME COLUMN currency TO amount_currency');
    }
}

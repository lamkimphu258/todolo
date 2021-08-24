<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210824143350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE todo ADD slug VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE todo ALTER name TYPE VARCHAR(100)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A0EB6A0989D9B62 ON todo (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_5A0EB6A0989D9B62');
        $this->addSql('ALTER TABLE todo DROP slug');
        $this->addSql('ALTER TABLE todo ALTER name TYPE VARCHAR(255)');
    }
}

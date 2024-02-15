<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215125803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture ADD experience_other_id INT NOT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8987C941FA FOREIGN KEY (experience_other_id) REFERENCES experience_other (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F8987C941FA ON picture (experience_other_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8987C941FA');
        $this->addSql('DROP INDEX IDX_16DB4F8987C941FA ON picture');
        $this->addSql('ALTER TABLE picture DROP experience_other_id');
    }
}

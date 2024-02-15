<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215124600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, experience_id INT DEFAULT NULL, portfolio_id INT DEFAULT NULL, techno_id INT DEFAULT NULL, formation_id INT DEFAULT NULL, project_id INT DEFAULT NULL, INDEX IDX_16DB4F8946E90E27 (experience_id), INDEX IDX_16DB4F89B96B5643 (portfolio_id), INDEX IDX_16DB4F8951F3C1BC (techno_id), INDEX IDX_16DB4F895200282E (formation_id), INDEX IDX_16DB4F89166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8946E90E27 FOREIGN KEY (experience_id) REFERENCES experience (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89B96B5643 FOREIGN KEY (portfolio_id) REFERENCES portfolio (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8951F3C1BC FOREIGN KEY (techno_id) REFERENCES techno (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F895200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F89166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8946E90E27');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89B96B5643');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8951F3C1BC');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F895200282E');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F89166D1F9C');
        $this->addSql('DROP TABLE picture');
    }
}

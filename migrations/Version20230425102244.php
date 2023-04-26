<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425102244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manager ADD manager_equi√pe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B96D1BD217 FOREIGN KEY (manager_equi√pe_id) REFERENCES equipe (id)');
        $this->addSql('CREATE INDEX IDX_FA2425B96D1BD217 ON manager (manager_equi√pe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE manager DROP FOREIGN KEY FK_FA2425B96D1BD217');
        $this->addSql('DROP INDEX IDX_FA2425B96D1BD217 ON manager');
        $this->addSql('ALTER TABLE manager DROP manager_equi√pe_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314175400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297D41CD9E7A');
        $this->addSql('DROP INDEX IDX_9657297D41CD9E7A ON work_time');
        $this->addSql('ALTER TABLE work_time CHANGE employer_id employee_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
        $this->addSql('CREATE INDEX IDX_9657297D8C03F15C ON work_time (employee_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE work_time DROP FOREIGN KEY FK_9657297D8C03F15C');
        $this->addSql('DROP INDEX IDX_9657297D8C03F15C ON work_time');
        $this->addSql('ALTER TABLE work_time CHANGE employee_id employer_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE work_time ADD CONSTRAINT FK_9657297D41CD9E7A FOREIGN KEY (employer_id) REFERENCES employee (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9657297D41CD9E7A ON work_time (employer_id)');
    }
}

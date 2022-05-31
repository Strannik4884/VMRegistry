<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220531001908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE vm_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE vmuser_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE vm (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, ssh_port SMALLINT NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BBFD16A7E3C61F9 ON vm (owner_id)');
        $this->addSql('CREATE TABLE vmuser (id INT NOT NULL, vm_id INT NOT NULL, login VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A1CCF5D6E0FCD18E ON vmuser (vm_id)');
        $this->addSql('ALTER TABLE vm ADD CONSTRAINT FK_BBFD16A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vmuser ADD CONSTRAINT FK_A1CCF5D6E0FCD18E FOREIGN KEY (vm_id) REFERENCES vm (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vm DROP CONSTRAINT FK_BBFD16A7E3C61F9');
        $this->addSql('ALTER TABLE vmuser DROP CONSTRAINT FK_A1CCF5D6E0FCD18E');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE vm_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE vmuser_id_seq CASCADE');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE vm');
        $this->addSql('DROP TABLE vmuser');
    }
}

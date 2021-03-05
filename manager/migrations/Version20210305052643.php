<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210305052643 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_network (id UUID NOT NULL, user_id VARCHAR(255) NOT NULL, network_name VARCHAR(32) DEFAULT NULL, identity VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_388999B6A76ED395 ON user_network (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_388999B6257EBD716A95E9C4 ON user_network (network_name, identity)');
        $this->addSql('CREATE TABLE user_user (id VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, password_hash VARCHAR(255) DEFAULT NULL, confirm_token VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, reset_token_value VARCHAR(255) DEFAULT NULL, reset_token_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7129A80E7927C74 ON user_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7129A80C4AC90FF ON user_user (reset_token_value)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7129A80A8C9AA51 ON user_user (confirm_token)');
        $this->addSql('COMMENT ON COLUMN user_user.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_user.reset_token_expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_network ADD CONSTRAINT FK_388999B6A76ED395 FOREIGN KEY (user_id) REFERENCES user_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE user_network DROP CONSTRAINT FK_388999B6A76ED395');
        $this->addSql('DROP TABLE user_network');
        $this->addSql('DROP TABLE user_user');
    }
}

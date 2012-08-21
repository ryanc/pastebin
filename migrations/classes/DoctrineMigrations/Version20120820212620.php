<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20120820212620 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE pastes (' .
                'id INTEGER PRIMARY KEY NOT NULL, ' .
                'timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, ' .
                'paste TEXT NOT NULL, ' .
                'token VARCHAR(50) NOT NULL, ' .
                'filename VARCHAR(100)' .
            ')'
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->addSql('DROP TABLE pastes');
    }
}

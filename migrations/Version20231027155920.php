<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231027155920 extends AbstractMigration
{
    /**
     * Returns a description of the migration.
     *
     * @return string Description of the migration.
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * Applies the migration changes to the database schema.
     *
     * @param Schema $schema The database schema to update.
     *
     * @return void
     */
    public function up(Schema $schema): void
    {
        // This up() migration is auto-generated, please modify it to your needs.
        $this->addSql('ALTER TABLE user CHANGE reset_token reset_token VARCHAR(100) DEFAULT NULL');
    }

    /**
     * Reverts the migration changes from the database schema.
     *
     * @param Schema $schema The database schema to update.
     *
     * @return void
     */
    public function down(Schema $schema): void
    {
        // This down() migration is auto-generated, please modify it to your needs.
        $this->addSql('ALTER TABLE user CHANGE reset_token reset_token VARCHAR(100) NOT NULL');
    }
}

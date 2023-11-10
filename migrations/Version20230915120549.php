<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915120549 extends AbstractMigration
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
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D8F0A91E5E237E06 ON trick (name)');
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
        $this->addSql('DROP INDEX UNIQ_D8F0A91E5E237E06 ON trick');
    }
}

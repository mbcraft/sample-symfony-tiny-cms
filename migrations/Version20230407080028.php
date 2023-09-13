<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230407080028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE downloadable_file ADD menu_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE downloadable_file ADD CONSTRAINT FK_F90A22BF9AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F90A22BF9AB44FE0 ON downloadable_file (menu_item_id)');
        $this->addSql('ALTER TABLE page ADD menu_item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB6209AB44FE0 FOREIGN KEY (menu_item_id) REFERENCES menu_item (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_140AB6209AB44FE0 ON page (menu_item_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE downloadable_file DROP FOREIGN KEY FK_F90A22BF9AB44FE0');
        $this->addSql('DROP INDEX UNIQ_F90A22BF9AB44FE0 ON downloadable_file');
        $this->addSql('ALTER TABLE downloadable_file DROP menu_item_id');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB6209AB44FE0');
        $this->addSql('DROP INDEX UNIQ_140AB6209AB44FE0 ON page');
        $this->addSql('ALTER TABLE page DROP menu_item_id');
    }
}

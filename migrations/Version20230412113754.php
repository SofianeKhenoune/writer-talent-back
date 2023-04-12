<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412113754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE toReadLater (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_947B6802A76ED395 (user_id), INDEX IDX_947B68024B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE toReadLater ADD CONSTRAINT FK_947B6802A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE toReadLater ADD CONSTRAINT FK_947B68024B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0C4B89032C');
        $this->addSql('ALTER TABLE test DROP FOREIGN KEY FK_D87F7E0CA76ED395');
        $this->addSql('DROP TABLE test');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_D87F7E0CA76ED395 (user_id), INDEX IDX_D87F7E0C4B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test ADD CONSTRAINT FK_D87F7E0CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE toReadLater DROP FOREIGN KEY FK_947B6802A76ED395');
        $this->addSql('ALTER TABLE toReadLater DROP FOREIGN KEY FK_947B68024B89032C');
        $this->addSql('DROP TABLE toReadLater');
    }
}

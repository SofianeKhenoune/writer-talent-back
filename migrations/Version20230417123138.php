<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230417123138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE liked (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_CA19CBBAA76ED395 (user_id), INDEX IDX_CA19CBBA4B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE liked ADD CONSTRAINT FK_CA19CBBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE liked ADD CONSTRAINT FK_CA19CBBA4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post DROP FOREIGN KEY FK_200B20444B89032C');
        $this->addSql('ALTER TABLE user_post DROP FOREIGN KEY FK_200B2044A76ED395');
        $this->addSql('DROP TABLE user_post');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_post (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_200B2044A76ED395 (user_id), INDEX IDX_200B20444B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_post ADD CONSTRAINT FK_200B20444B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post ADD CONSTRAINT FK_200B2044A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE liked DROP FOREIGN KEY FK_CA19CBBAA76ED395');
        $this->addSql('ALTER TABLE liked DROP FOREIGN KEY FK_CA19CBBA4B89032C');
        $this->addSql('DROP TABLE liked');
    }
}

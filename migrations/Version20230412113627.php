<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230412113627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ToReadLater DROP FOREIGN KEY FK_12E7E7D5A76ED395');
        $this->addSql('ALTER TABLE ToReadLater DROP FOREIGN KEY FK_12E7E7D54B89032C');
        $this->addSql('DROP INDEX idx_12e7e7d5a76ed395 ON ToReadLater');
        $this->addSql('CREATE INDEX IDX_947B6802A76ED395 ON ToReadLater (user_id)');
        $this->addSql('DROP INDEX idx_12e7e7d54b89032c ON ToReadLater');
        $this->addSql('CREATE INDEX IDX_947B68024B89032C ON ToReadLater (post_id)');
        $this->addSql('ALTER TABLE ToReadLater ADD CONSTRAINT FK_12E7E7D5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ToReadLater ADD CONSTRAINT FK_12E7E7D54B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE toReadLater DROP FOREIGN KEY FK_947B6802A76ED395');
        $this->addSql('ALTER TABLE toReadLater DROP FOREIGN KEY FK_947B68024B89032C');
        $this->addSql('DROP INDEX idx_947b6802a76ed395 ON toReadLater');
        $this->addSql('CREATE INDEX IDX_12E7E7D5A76ED395 ON toReadLater (user_id)');
        $this->addSql('DROP INDEX idx_947b68024b89032c ON toReadLater');
        $this->addSql('CREATE INDEX IDX_12E7E7D54B89032C ON toReadLater (post_id)');
        $this->addSql('ALTER TABLE toReadLater ADD CONSTRAINT FK_947B6802A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE toReadLater ADD CONSTRAINT FK_947B68024B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
    }
}

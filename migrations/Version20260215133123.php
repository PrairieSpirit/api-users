<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260215133123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added table users with unique login+password';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE users (
                id INT AUTO_INCREMENT NOT NULL,
                login VARCHAR(8) NOT NULL,
                phone VARCHAR(8) NOT NULL,
                pas VARCHAR(8) NOT NULL,
                UNIQUE INDEX uniq_users_login_pas (login, pas),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE users');
    }
}

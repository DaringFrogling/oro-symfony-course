<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211208231458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS product (
                id INTEGER PRIMARY KEY,
   	            title TEXT NOT NULL,
	            created_at DATE DEFAULT NULL
            )'
        );
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS product_image (
                id INTEGER PRIMARY KEY,
                product_id INTEGER NOT NULL REFERENCES product(id),
   	            title TEXT NOT NULL,
	            created_at DATE DEFAULT NULL
            )'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS product');
        $this->addSql('DROP TABLE IF EXISTS product_image');
    }
}

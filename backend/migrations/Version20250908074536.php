<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250908074536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auction (id SERIAL NOT NULL, product_id INT NOT NULL, winner_customer_id INT DEFAULT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, current_price NUMERIC(5, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DEE4F5934584665A ON auction (product_id)');
        $this->addSql('CREATE INDEX IDX_DEE4F5938FAB3BE1 ON auction (winner_customer_id)');
        $this->addSql('COMMENT ON COLUMN auction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auction.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE bid (id SERIAL NOT NULL, auction_id INT DEFAULT NULL, customer_id INT NOT NULL, amount NUMERIC(5, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4AF2B3F357B8F0DE ON bid (auction_id)');
        $this->addSql('CREATE INDEX IDX_4AF2B3F39395C3F3 ON bid (customer_id)');
        $this->addSql('COMMENT ON COLUMN bid.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN bid.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE customer (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN customer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN customer.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, vendor_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, starting_price NUMERIC(5, 2) NOT NULL, image_url VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D34A04ADF603EE73 ON product (vendor_id)');
        $this->addSql('COMMENT ON COLUMN product.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN product.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE transaction (id SERIAL NOT NULL, auction_id INT NOT NULL, customer_id INT NOT NULL, vendor_id INT NOT NULL, amount NUMERIC(5, 2) NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D157B8F0DE ON transaction (auction_id)');
        $this->addSql('CREATE INDEX IDX_723705D19395C3F3 ON transaction (customer_id)');
        $this->addSql('CREATE INDEX IDX_723705D1F603EE73 ON transaction (vendor_id)');
        $this->addSql('COMMENT ON COLUMN transaction.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN transaction.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE vendor (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN vendor.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN vendor.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F5934584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE auction ADD CONSTRAINT FK_DEE4F5938FAB3BE1 FOREIGN KEY (winner_customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F357B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bid ADD CONSTRAINT FK_4AF2B3F39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D157B8F0DE FOREIGN KEY (auction_id) REFERENCES auction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE auction DROP CONSTRAINT FK_DEE4F5934584665A');
        $this->addSql('ALTER TABLE auction DROP CONSTRAINT FK_DEE4F5938FAB3BE1');
        $this->addSql('ALTER TABLE bid DROP CONSTRAINT FK_4AF2B3F357B8F0DE');
        $this->addSql('ALTER TABLE bid DROP CONSTRAINT FK_4AF2B3F39395C3F3');
        $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADF603EE73');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D157B8F0DE');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D19395C3F3');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT FK_723705D1F603EE73');
        $this->addSql('DROP TABLE auction');
        $this->addSql('DROP TABLE bid');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE vendor');
    }
}

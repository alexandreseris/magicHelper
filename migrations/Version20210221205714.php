<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210221205714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist (name VARCHAR(255) NOT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE card (id_scryfall VARCHAR(36) NOT NULL, layout_id VARCHAR(50) NOT NULL, rarity_id VARCHAR(50) NOT NULL, set_id VARCHAR(10) NOT NULL, id_oracle VARCHAR(36) NOT NULL, id_arena INTEGER DEFAULT NULL, released_date DATE DEFAULT NULL, PRIMARY KEY(id_scryfall))');
        $this->addSql('CREATE INDEX IDX_161498D38C22AA1A ON card (layout_id)');
        $this->addSql('CREATE INDEX IDX_161498D3F3747573 ON card (rarity_id)');
        $this->addSql('CREATE INDEX IDX_161498D310FB0D18 ON card (set_id)');
        $this->addSql('CREATE TABLE Card_ColorIdentity (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, color_id))');
        $this->addSql('CREATE INDEX IDX_2A7FDD254ACC9A20 ON Card_ColorIdentity (card_id)');
        $this->addSql('CREATE INDEX IDX_2A7FDD257ADA1FB5 ON Card_ColorIdentity (color_id)');
        $this->addSql('CREATE TABLE Card_ProducedMana (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, color_id))');
        $this->addSql('CREATE INDEX IDX_75D8D4034ACC9A20 ON Card_ProducedMana (card_id)');
        $this->addSql('CREATE INDEX IDX_75D8D4037ADA1FB5 ON Card_ProducedMana (color_id)');
        $this->addSql('CREATE TABLE Card_Keyword (card_id VARCHAR(36) NOT NULL, keyword_id VARCHAR(100) NOT NULL, PRIMARY KEY(card_id, keyword_id))');
        $this->addSql('CREATE INDEX IDX_DA0519294ACC9A20 ON Card_Keyword (card_id)');
        $this->addSql('CREATE INDEX IDX_DA051929115D4552 ON Card_Keyword (keyword_id)');
        $this->addSql('CREATE TABLE Card_Related (card_id VARCHAR(36) NOT NULL, relatedCard_id VARCHAR(36) NOT NULL, PRIMARY KEY(card_id, relatedCard_id))');
        $this->addSql('CREATE INDEX IDX_E0C118824ACC9A20 ON Card_Related (card_id)');
        $this->addSql('CREATE INDEX IDX_E0C11882D08E3C99 ON Card_Related (relatedCard_id)');
        $this->addSql('CREATE TABLE card_legality (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, card_id VARCHAR(36) NOT NULL, legalityType_id VARCHAR(50) NOT NULL, legalityValue_id VARCHAR(50) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_67066B3D4ACC9A20 ON card_legality (card_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DE6E0AB93 ON card_legality (legalityType_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DF9031785 ON card_legality (legalityValue_id)');
        $this->addSql('CREATE TABLE color (code VARCHAR(1) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE data_date (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE face ("index" INTEGER NOT NULL, card_id VARCHAR(36) NOT NULL, artist_id VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, type_line VARCHAR(100) NOT NULL, oracle_text VARCHAR(2000) DEFAULT NULL, printed_text VARCHAR(2000) DEFAULT NULL, power VARCHAR(5) DEFAULT NULL, toughness VARCHAR(5) DEFAULT NULL, PRIMARY KEY(card_id, "index"))');
        $this->addSql('CREATE INDEX IDX_5147B674ACC9A20 ON face (card_id)');
        $this->addSql('CREATE INDEX IDX_5147B67B7970CF8 ON face (artist_id)');
        $this->addSql('CREATE TABLE Face_Color (card_id VARCHAR(36) NOT NULL, face_index INTEGER NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, face_index, color_id))');
        $this->addSql('CREATE INDEX IDX_20B164BE4ACC9A2048086782 ON Face_Color (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_20B164BE7ADA1FB5 ON Face_Color (color_id)');
        $this->addSql('CREATE TABLE Face_ManaCost (card_id VARCHAR(36) NOT NULL, face_index INTEGER NOT NULL, symbol_id VARCHAR(20) NOT NULL, PRIMARY KEY(card_id, face_index, symbol_id))');
        $this->addSql('CREATE INDEX IDX_625DDD5D4ACC9A2048086782 ON Face_ManaCost (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_625DDD5DC0F75674 ON Face_ManaCost (symbol_id)');
        $this->addSql('CREATE TABLE keyword (name VARCHAR(100) NOT NULL, is_ability BOOLEAN NOT NULL, is_action BOOLEAN NOT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE layout (code VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE legality_type (name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE legality_value (name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE rarity (name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE "set" (code VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, released_date DATE DEFAULT NULL, setType_id VARCHAR(50) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE INDEX IDX_E61425DC5787671D ON "set" (setType_id)');
        $this->addSql('CREATE TABLE set_type (code VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE symbol (code VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, is_funny BOOLEAN NOT NULL, is_mana BOOLEAN NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, cmc NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE Symbol_Color (symbol_id VARCHAR(20) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(symbol_id, color_id))');
        $this->addSql('CREATE INDEX IDX_3A63BA53C0F75674 ON Symbol_Color (symbol_id)');
        $this->addSql('CREATE INDEX IDX_3A63BA537ADA1FB5 ON Symbol_Color (color_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE Card_ColorIdentity');
        $this->addSql('DROP TABLE Card_ProducedMana');
        $this->addSql('DROP TABLE Card_Keyword');
        $this->addSql('DROP TABLE Card_Related');
        $this->addSql('DROP TABLE card_legality');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE data_date');
        $this->addSql('DROP TABLE face');
        $this->addSql('DROP TABLE Face_Color');
        $this->addSql('DROP TABLE Face_ManaCost');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE layout');
        $this->addSql('DROP TABLE legality_type');
        $this->addSql('DROP TABLE legality_value');
        $this->addSql('DROP TABLE rarity');
        $this->addSql('DROP TABLE "set"');
        $this->addSql('DROP TABLE set_type');
        $this->addSql('DROP TABLE symbol');
        $this->addSql('DROP TABLE Symbol_Color');
    }
}

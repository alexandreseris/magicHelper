<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210425232614 extends AbstractMigration
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
        $this->addSql('CREATE TABLE card_colorIdentity (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, color_id))');
        $this->addSql('CREATE INDEX IDX_C4472EE04ACC9A20 ON card_colorIdentity (card_id)');
        $this->addSql('CREATE INDEX IDX_C4472EE07ADA1FB5 ON card_colorIdentity (color_id)');
        $this->addSql('CREATE TABLE card_producedMana (card_id VARCHAR(36) NOT NULL, symbol_id VARCHAR(20) NOT NULL, PRIMARY KEY(card_id, symbol_id))');
        $this->addSql('CREATE INDEX IDX_434A3D014ACC9A20 ON card_producedMana (card_id)');
        $this->addSql('CREATE INDEX IDX_434A3D01C0F75674 ON card_producedMana (symbol_id)');
        $this->addSql('CREATE TABLE card_keyword (card_id VARCHAR(36) NOT NULL, keyword_id VARCHAR(100) NOT NULL, PRIMARY KEY(card_id, keyword_id))');
        $this->addSql('CREATE INDEX IDX_D89FB4D4ACC9A20 ON card_keyword (card_id)');
        $this->addSql('CREATE INDEX IDX_D89FB4D115D4552 ON card_keyword (keyword_id)');
        $this->addSql('CREATE TABLE card_related (card_id VARCHAR(36) NOT NULL, relatedCard_id VARCHAR(36) NOT NULL, PRIMARY KEY(card_id, relatedCard_id))');
        $this->addSql('CREATE INDEX IDX_374DFAE64ACC9A20 ON card_related (card_id)');
        $this->addSql('CREATE INDEX IDX_374DFAE6D08E3C99 ON card_related (relatedCard_id)');
        $this->addSql('CREATE TABLE card_legality (card_id VARCHAR(36) NOT NULL, format_id VARCHAR(50) NOT NULL, legality_id VARCHAR(50) NOT NULL, PRIMARY KEY(card_id, format_id, legality_id))');
        $this->addSql('CREATE INDEX IDX_67066B3D4ACC9A20 ON card_legality (card_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DD629F605 ON card_legality (format_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DC24CFB57 ON card_legality (legality_id)');
        $this->addSql('CREATE TABLE color (code VARCHAR(1) NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE data_date (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, updated_at DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE face (face_id VARCHAR(40) NOT NULL, card_id VARCHAR(36) NOT NULL, artist_id VARCHAR(255) DEFAULT NULL, face_index INTEGER NOT NULL, image_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, type_line VARCHAR(100) NOT NULL, oracle_text VARCHAR(2000) DEFAULT NULL, printed_text VARCHAR(2000) DEFAULT NULL, power_value VARCHAR(5) DEFAULT NULL, toughness_value VARCHAR(5) DEFAULT NULL, image_local VARCHAR(255) DEFAULT NULL, PRIMARY KEY(face_id))');
        $this->addSql('CREATE INDEX IDX_5147B674ACC9A20 ON face (card_id)');
        $this->addSql('CREATE INDEX IDX_5147B67B7970CF8 ON face (artist_id)');
        $this->addSql('CREATE TABLE face_color (face_id VARCHAR(40) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(face_id, color_id))');
        $this->addSql('CREATE INDEX IDX_AE2D486AFDC86CD0 ON face_color (face_id)');
        $this->addSql('CREATE INDEX IDX_AE2D486A7ADA1FB5 ON face_color (color_id)');
        $this->addSql('CREATE TABLE face_mana_cost (face_id VARCHAR(40) NOT NULL, symbol_id VARCHAR(20) NOT NULL, quantity INTEGER NOT NULL, PRIMARY KEY(face_id, symbol_id))');
        $this->addSql('CREATE INDEX IDX_6AFCD9AFFDC86CD0 ON face_mana_cost (face_id)');
        $this->addSql('CREATE INDEX IDX_6AFCD9AFC0F75674 ON face_mana_cost (symbol_id)');
        $this->addSql('CREATE TABLE format (code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE keyword (name VARCHAR(100) NOT NULL, is_ability BOOLEAN NOT NULL, is_action BOOLEAN NOT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE layout (code VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE legality (code VARCHAR(50) NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE rarity (name VARCHAR(50) NOT NULL, color VARCHAR(10) DEFAULT NULL, index_value INTEGER DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('CREATE TABLE setOfCard (code VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, released_date DATE DEFAULT NULL, icon_url VARCHAR(255) NOT NULL, icon_local VARCHAR(255) DEFAULT NULL, setType_id VARCHAR(50) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE INDEX IDX_D01563FC5787671D ON setOfCard (setType_id)');
        $this->addSql('CREATE TABLE set_type (code VARCHAR(50) NOT NULL, name VARCHAR(100) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE symbol (code VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, is_funny BOOLEAN NOT NULL, is_mana BOOLEAN NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, cmc NUMERIC(10, 2) DEFAULT NULL, icon_local VARCHAR(255) DEFAULT NULL, code_variant VARCHAR(20) DEFAULT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE symbol_color (symbol_id VARCHAR(20) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(symbol_id, color_id))');
        $this->addSql('CREATE INDEX IDX_E3934EAFC0F75674 ON symbol_color (symbol_id)');
        $this->addSql('CREATE INDEX IDX_E3934EAF7ADA1FB5 ON symbol_color (color_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE artist');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE card_colorIdentity');
        $this->addSql('DROP TABLE card_producedMana');
        $this->addSql('DROP TABLE card_keyword');
        $this->addSql('DROP TABLE card_related');
        $this->addSql('DROP TABLE card_legality');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE data_date');
        $this->addSql('DROP TABLE face');
        $this->addSql('DROP TABLE face_color');
        $this->addSql('DROP TABLE face_mana_cost');
        $this->addSql('DROP TABLE format');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE layout');
        $this->addSql('DROP TABLE legality');
        $this->addSql('DROP TABLE rarity');
        $this->addSql('DROP TABLE setOfCard');
        $this->addSql('DROP TABLE set_type');
        $this->addSql('DROP TABLE symbol');
        $this->addSql('DROP TABLE symbol_color');
    }
}

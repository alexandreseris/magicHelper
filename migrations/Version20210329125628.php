<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210329125628 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_161498D38C22AA1A');
        $this->addSql('DROP INDEX IDX_161498D3F3747573');
        $this->addSql('DROP INDEX IDX_161498D310FB0D18');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card AS SELECT id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date FROM card');
        $this->addSql('DROP TABLE card');
        $this->addSql('CREATE TABLE card (id_scryfall VARCHAR(36) NOT NULL COLLATE BINARY, layout_id VARCHAR(50) NOT NULL COLLATE BINARY, rarity_id VARCHAR(50) NOT NULL COLLATE BINARY, set_id VARCHAR(10) NOT NULL COLLATE BINARY, id_oracle VARCHAR(36) NOT NULL COLLATE BINARY, id_arena INTEGER DEFAULT NULL, released_date DATE DEFAULT NULL, PRIMARY KEY(id_scryfall), CONSTRAINT FK_161498D38C22AA1A FOREIGN KEY (layout_id) REFERENCES layout (code) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_161498D3F3747573 FOREIGN KEY (rarity_id) REFERENCES rarity (name) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_161498D310FB0D18 FOREIGN KEY (set_id) REFERENCES setOfCard (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card (id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date) SELECT id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date FROM __temp__card');
        $this->addSql('DROP TABLE __temp__card');
        $this->addSql('CREATE INDEX IDX_161498D38C22AA1A ON card (layout_id)');
        $this->addSql('CREATE INDEX IDX_161498D3F3747573 ON card (rarity_id)');
        $this->addSql('CREATE INDEX IDX_161498D310FB0D18 ON card (set_id)');
        $this->addSql('DROP INDEX IDX_C4472EE04ACC9A20');
        $this->addSql('DROP INDEX IDX_C4472EE07ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_colorIdentity AS SELECT card_id, color_id FROM card_colorIdentity');
        $this->addSql('DROP TABLE card_colorIdentity');
        $this->addSql('CREATE TABLE card_colorIdentity (card_id VARCHAR(36) NOT NULL COLLATE BINARY, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, color_id), CONSTRAINT FK_C4472EE04ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C4472EE07ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card_colorIdentity (card_id, color_id) SELECT card_id, color_id FROM __temp__card_colorIdentity');
        $this->addSql('DROP TABLE __temp__card_colorIdentity');
        $this->addSql('CREATE INDEX IDX_C4472EE04ACC9A20 ON card_colorIdentity (card_id)');
        $this->addSql('CREATE INDEX IDX_C4472EE07ADA1FB5 ON card_colorIdentity (color_id)');
        $this->addSql('DROP INDEX IDX_434A3D014ACC9A20');
        $this->addSql('DROP INDEX IDX_434A3D017ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_producedMana AS SELECT card_id FROM card_producedMana');
        $this->addSql('DROP TABLE card_producedMana');
        $this->addSql('CREATE TABLE card_producedMana (card_id VARCHAR(36) NOT NULL COLLATE BINARY, symbol_id VARCHAR(20) NOT NULL, PRIMARY KEY(card_id, symbol_id), CONSTRAINT FK_434A3D014ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_434A3D01C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card_producedMana (card_id) SELECT card_id FROM __temp__card_producedMana');
        $this->addSql('DROP TABLE __temp__card_producedMana');
        $this->addSql('CREATE INDEX IDX_434A3D014ACC9A20 ON card_producedMana (card_id)');
        $this->addSql('CREATE INDEX IDX_434A3D01C0F75674 ON card_producedMana (symbol_id)');
        $this->addSql('DROP INDEX IDX_D89FB4D4ACC9A20');
        $this->addSql('DROP INDEX IDX_D89FB4D115D4552');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_keyword AS SELECT card_id, keyword_id FROM card_keyword');
        $this->addSql('DROP TABLE card_keyword');
        $this->addSql('CREATE TABLE card_keyword (card_id VARCHAR(36) NOT NULL COLLATE BINARY, keyword_id VARCHAR(100) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, keyword_id), CONSTRAINT FK_D89FB4D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D89FB4D115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card_keyword (card_id, keyword_id) SELECT card_id, keyword_id FROM __temp__card_keyword');
        $this->addSql('DROP TABLE __temp__card_keyword');
        $this->addSql('CREATE INDEX IDX_D89FB4D4ACC9A20 ON card_keyword (card_id)');
        $this->addSql('CREATE INDEX IDX_D89FB4D115D4552 ON card_keyword (keyword_id)');
        $this->addSql('DROP INDEX IDX_374DFAE64ACC9A20');
        $this->addSql('DROP INDEX IDX_374DFAE6D08E3C99');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_related AS SELECT card_id, relatedCard_id FROM card_related');
        $this->addSql('DROP TABLE card_related');
        $this->addSql('CREATE TABLE card_related (card_id VARCHAR(36) NOT NULL COLLATE BINARY, relatedCard_id VARCHAR(36) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, relatedCard_id), CONSTRAINT FK_374DFAE64ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_374DFAE6D08E3C99 FOREIGN KEY (relatedCard_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card_related (card_id, relatedCard_id) SELECT card_id, relatedCard_id FROM __temp__card_related');
        $this->addSql('DROP TABLE __temp__card_related');
        $this->addSql('CREATE INDEX IDX_374DFAE64ACC9A20 ON card_related (card_id)');
        $this->addSql('CREATE INDEX IDX_374DFAE6D08E3C99 ON card_related (relatedCard_id)');
        $this->addSql('DROP INDEX IDX_67066B3D4ACC9A20');
        $this->addSql('DROP INDEX IDX_67066B3DE6E0AB93');
        $this->addSql('DROP INDEX IDX_67066B3DF9031785');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_legality AS SELECT card_id, legalityType_id, legalityValue_id FROM card_legality');
        $this->addSql('DROP TABLE card_legality');
        $this->addSql('CREATE TABLE card_legality (card_id VARCHAR(36) NOT NULL COLLATE BINARY, legalityType_id VARCHAR(50) NOT NULL COLLATE BINARY, legalityValue_id VARCHAR(50) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, legalityType_id, legalityValue_id), CONSTRAINT FK_67066B3D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67066B3DE6E0AB93 FOREIGN KEY (legalityType_id) REFERENCES legality_type (name) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67066B3DF9031785 FOREIGN KEY (legalityValue_id) REFERENCES legality_value (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card_legality (card_id, legalityType_id, legalityValue_id) SELECT card_id, legalityType_id, legalityValue_id FROM __temp__card_legality');
        $this->addSql('DROP TABLE __temp__card_legality');
        $this->addSql('CREATE INDEX IDX_67066B3D4ACC9A20 ON card_legality (card_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DE6E0AB93 ON card_legality (legalityType_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DF9031785 ON card_legality (legalityValue_id)');
        $this->addSql('DROP INDEX IDX_5147B674ACC9A20');
        $this->addSql('DROP INDEX IDX_5147B67B7970CF8');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face AS SELECT card_id, face_index, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness, image_local FROM face');
        $this->addSql('DROP TABLE face');
        $this->addSql('CREATE TABLE face (card_id VARCHAR(36) NOT NULL COLLATE BINARY, face_index INTEGER NOT NULL, artist_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, image_url VARCHAR(255) DEFAULT NULL COLLATE BINARY, name VARCHAR(255) NOT NULL COLLATE BINARY, type_line VARCHAR(100) NOT NULL COLLATE BINARY, oracle_text VARCHAR(2000) DEFAULT NULL COLLATE BINARY, printed_text VARCHAR(2000) DEFAULT NULL COLLATE BINARY, power VARCHAR(5) DEFAULT NULL COLLATE BINARY, toughness VARCHAR(5) DEFAULT NULL COLLATE BINARY, image_local VARCHAR(255) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(card_id, face_index), CONSTRAINT FK_5147B674ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id_scryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5147B67B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO face (card_id, face_index, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness, image_local) SELECT card_id, face_index, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness, image_local FROM __temp__face');
        $this->addSql('DROP TABLE __temp__face');
        $this->addSql('CREATE INDEX IDX_5147B674ACC9A20 ON face (card_id)');
        $this->addSql('CREATE INDEX IDX_5147B67B7970CF8 ON face (artist_id)');
        $this->addSql('DROP INDEX IDX_AE2D486A4ACC9A2048086782');
        $this->addSql('DROP INDEX IDX_AE2D486A7ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face_color AS SELECT card_id, face_index, color_id FROM face_color');
        $this->addSql('DROP TABLE face_color');
        $this->addSql('CREATE TABLE face_color (card_id VARCHAR(36) NOT NULL COLLATE BINARY, face_index INTEGER NOT NULL, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, face_index, color_id), CONSTRAINT FK_AE2D486A4ACC9A2048086782 FOREIGN KEY (card_id, face_index) REFERENCES face (card_id, face_index) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_AE2D486A7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO face_color (card_id, face_index, color_id) SELECT card_id, face_index, color_id FROM __temp__face_color');
        $this->addSql('DROP TABLE __temp__face_color');
        $this->addSql('CREATE INDEX IDX_AE2D486A4ACC9A2048086782 ON face_color (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_AE2D486A7ADA1FB5 ON face_color (color_id)');
        $this->addSql('DROP INDEX IDX_2855F4FE4ACC9A2048086782');
        $this->addSql('DROP INDEX IDX_2855F4FEC0F75674');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face_manaCost AS SELECT card_id, face_index, symbol_id FROM face_manaCost');
        $this->addSql('DROP TABLE face_manaCost');
        $this->addSql('CREATE TABLE face_manaCost (card_id VARCHAR(36) NOT NULL COLLATE BINARY, face_index INTEGER NOT NULL, symbol_id VARCHAR(20) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, face_index, symbol_id), CONSTRAINT FK_2855F4FE4ACC9A2048086782 FOREIGN KEY (card_id, face_index) REFERENCES face (card_id, face_index) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2855F4FEC0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO face_manaCost (card_id, face_index, symbol_id) SELECT card_id, face_index, symbol_id FROM __temp__face_manaCost');
        $this->addSql('DROP TABLE __temp__face_manaCost');
        $this->addSql('CREATE INDEX IDX_2855F4FE4ACC9A2048086782 ON face_manaCost (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_2855F4FEC0F75674 ON face_manaCost (symbol_id)');
        $this->addSql('DROP INDEX IDX_D01563FC5787671D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__setOfCard AS SELECT code, name, released_date, setType_id, icon_url, icon_local FROM setOfCard');
        $this->addSql('DROP TABLE setOfCard');
        $this->addSql('CREATE TABLE setOfCard (code VARCHAR(10) NOT NULL COLLATE BINARY, name VARCHAR(100) NOT NULL COLLATE BINARY, released_date DATE DEFAULT NULL, setType_id VARCHAR(50) NOT NULL COLLATE BINARY, icon_url VARCHAR(255) NOT NULL COLLATE BINARY, icon_local VARCHAR(255) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(code), CONSTRAINT FK_D01563FC5787671D FOREIGN KEY (setType_id) REFERENCES set_type (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO setOfCard (code, name, released_date, setType_id, icon_url, icon_local) SELECT code, name, released_date, setType_id, icon_url, icon_local FROM __temp__setOfCard');
        $this->addSql('DROP TABLE __temp__setOfCard');
        $this->addSql('CREATE INDEX IDX_D01563FC5787671D ON setOfCard (setType_id)');
        $this->addSql('ALTER TABLE symbol ADD COLUMN code_variant VARCHAR(20) DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_E3934EAFC0F75674');
        $this->addSql('DROP INDEX IDX_E3934EAF7ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__symbol_color AS SELECT symbol_id, color_id FROM symbol_color');
        $this->addSql('DROP TABLE symbol_color');
        $this->addSql('CREATE TABLE symbol_color (symbol_id VARCHAR(20) NOT NULL COLLATE BINARY, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(symbol_id, color_id), CONSTRAINT FK_E3934EAFC0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (code) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E3934EAF7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO symbol_color (symbol_id, color_id) SELECT symbol_id, color_id FROM __temp__symbol_color');
        $this->addSql('DROP TABLE __temp__symbol_color');
        $this->addSql('CREATE INDEX IDX_E3934EAFC0F75674 ON symbol_color (symbol_id)');
        $this->addSql('CREATE INDEX IDX_E3934EAF7ADA1FB5 ON symbol_color (color_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_161498D38C22AA1A');
        $this->addSql('DROP INDEX IDX_161498D3F3747573');
        $this->addSql('DROP INDEX IDX_161498D310FB0D18');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card AS SELECT id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date FROM card');
        $this->addSql('DROP TABLE card');
        $this->addSql('CREATE TABLE card (id_scryfall VARCHAR(36) NOT NULL, layout_id VARCHAR(50) NOT NULL, rarity_id VARCHAR(50) NOT NULL, set_id VARCHAR(10) NOT NULL, id_oracle VARCHAR(36) NOT NULL, id_arena INTEGER DEFAULT NULL, released_date DATE DEFAULT NULL, PRIMARY KEY(id_scryfall))');
        $this->addSql('INSERT INTO card (id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date) SELECT id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date FROM __temp__card');
        $this->addSql('DROP TABLE __temp__card');
        $this->addSql('CREATE INDEX IDX_161498D38C22AA1A ON card (layout_id)');
        $this->addSql('CREATE INDEX IDX_161498D3F3747573 ON card (rarity_id)');
        $this->addSql('CREATE INDEX IDX_161498D310FB0D18 ON card (set_id)');
        $this->addSql('DROP INDEX IDX_C4472EE04ACC9A20');
        $this->addSql('DROP INDEX IDX_C4472EE07ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_colorIdentity AS SELECT card_id, color_id FROM card_colorIdentity');
        $this->addSql('DROP TABLE card_colorIdentity');
        $this->addSql('CREATE TABLE card_colorIdentity (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, color_id))');
        $this->addSql('INSERT INTO card_colorIdentity (card_id, color_id) SELECT card_id, color_id FROM __temp__card_colorIdentity');
        $this->addSql('DROP TABLE __temp__card_colorIdentity');
        $this->addSql('CREATE INDEX IDX_C4472EE04ACC9A20 ON card_colorIdentity (card_id)');
        $this->addSql('CREATE INDEX IDX_C4472EE07ADA1FB5 ON card_colorIdentity (color_id)');
        $this->addSql('DROP INDEX IDX_D89FB4D4ACC9A20');
        $this->addSql('DROP INDEX IDX_D89FB4D115D4552');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_keyword AS SELECT card_id, keyword_id FROM card_keyword');
        $this->addSql('DROP TABLE card_keyword');
        $this->addSql('CREATE TABLE card_keyword (card_id VARCHAR(36) NOT NULL, keyword_id VARCHAR(100) NOT NULL, PRIMARY KEY(card_id, keyword_id))');
        $this->addSql('INSERT INTO card_keyword (card_id, keyword_id) SELECT card_id, keyword_id FROM __temp__card_keyword');
        $this->addSql('DROP TABLE __temp__card_keyword');
        $this->addSql('CREATE INDEX IDX_D89FB4D4ACC9A20 ON card_keyword (card_id)');
        $this->addSql('CREATE INDEX IDX_D89FB4D115D4552 ON card_keyword (keyword_id)');
        $this->addSql('DROP INDEX IDX_67066B3D4ACC9A20');
        $this->addSql('DROP INDEX IDX_67066B3DE6E0AB93');
        $this->addSql('DROP INDEX IDX_67066B3DF9031785');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_legality AS SELECT card_id, legalityType_id, legalityValue_id FROM card_legality');
        $this->addSql('DROP TABLE card_legality');
        $this->addSql('CREATE TABLE card_legality (card_id VARCHAR(36) NOT NULL, legalityType_id VARCHAR(50) NOT NULL, legalityValue_id VARCHAR(50) NOT NULL, PRIMARY KEY(card_id, legalityType_id, legalityValue_id))');
        $this->addSql('INSERT INTO card_legality (card_id, legalityType_id, legalityValue_id) SELECT card_id, legalityType_id, legalityValue_id FROM __temp__card_legality');
        $this->addSql('DROP TABLE __temp__card_legality');
        $this->addSql('CREATE INDEX IDX_67066B3D4ACC9A20 ON card_legality (card_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DE6E0AB93 ON card_legality (legalityType_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DF9031785 ON card_legality (legalityValue_id)');
        $this->addSql('DROP INDEX IDX_434A3D014ACC9A20');
        $this->addSql('DROP INDEX IDX_434A3D01C0F75674');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_producedMana AS SELECT card_id FROM card_producedMana');
        $this->addSql('DROP TABLE card_producedMana');
        $this->addSql('CREATE TABLE card_producedMana (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, color_id))');
        $this->addSql('INSERT INTO card_producedMana (card_id) SELECT card_id FROM __temp__card_producedMana');
        $this->addSql('DROP TABLE __temp__card_producedMana');
        $this->addSql('CREATE INDEX IDX_434A3D014ACC9A20 ON card_producedMana (card_id)');
        $this->addSql('CREATE INDEX IDX_434A3D017ADA1FB5 ON card_producedMana (color_id)');
        $this->addSql('DROP INDEX IDX_374DFAE64ACC9A20');
        $this->addSql('DROP INDEX IDX_374DFAE6D08E3C99');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_related AS SELECT card_id, relatedCard_id FROM card_related');
        $this->addSql('DROP TABLE card_related');
        $this->addSql('CREATE TABLE card_related (card_id VARCHAR(36) NOT NULL, relatedCard_id VARCHAR(36) NOT NULL, PRIMARY KEY(card_id, relatedCard_id))');
        $this->addSql('INSERT INTO card_related (card_id, relatedCard_id) SELECT card_id, relatedCard_id FROM __temp__card_related');
        $this->addSql('DROP TABLE __temp__card_related');
        $this->addSql('CREATE INDEX IDX_374DFAE64ACC9A20 ON card_related (card_id)');
        $this->addSql('CREATE INDEX IDX_374DFAE6D08E3C99 ON card_related (relatedCard_id)');
        $this->addSql('DROP INDEX IDX_5147B674ACC9A20');
        $this->addSql('DROP INDEX IDX_5147B67B7970CF8');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face AS SELECT face_index, card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness, image_local FROM face');
        $this->addSql('DROP TABLE face');
        $this->addSql('CREATE TABLE face (face_index INTEGER NOT NULL, card_id VARCHAR(36) NOT NULL, artist_id VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, type_line VARCHAR(100) NOT NULL, oracle_text VARCHAR(2000) DEFAULT NULL, printed_text VARCHAR(2000) DEFAULT NULL, power VARCHAR(5) DEFAULT NULL, toughness VARCHAR(5) DEFAULT NULL, image_local VARCHAR(255) DEFAULT NULL, PRIMARY KEY(card_id, face_index))');
        $this->addSql('INSERT INTO face (face_index, card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness, image_local) SELECT face_index, card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness, image_local FROM __temp__face');
        $this->addSql('DROP TABLE __temp__face');
        $this->addSql('CREATE INDEX IDX_5147B674ACC9A20 ON face (card_id)');
        $this->addSql('CREATE INDEX IDX_5147B67B7970CF8 ON face (artist_id)');
        $this->addSql('DROP INDEX IDX_AE2D486A4ACC9A2048086782');
        $this->addSql('DROP INDEX IDX_AE2D486A7ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face_color AS SELECT card_id, face_index, color_id FROM face_color');
        $this->addSql('DROP TABLE face_color');
        $this->addSql('CREATE TABLE face_color (card_id VARCHAR(36) NOT NULL, face_index INTEGER NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, face_index, color_id))');
        $this->addSql('INSERT INTO face_color (card_id, face_index, color_id) SELECT card_id, face_index, color_id FROM __temp__face_color');
        $this->addSql('DROP TABLE __temp__face_color');
        $this->addSql('CREATE INDEX IDX_AE2D486A4ACC9A2048086782 ON face_color (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_AE2D486A7ADA1FB5 ON face_color (color_id)');
        $this->addSql('DROP INDEX IDX_2855F4FE4ACC9A2048086782');
        $this->addSql('DROP INDEX IDX_2855F4FEC0F75674');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face_manaCost AS SELECT card_id, face_index, symbol_id FROM face_manaCost');
        $this->addSql('DROP TABLE face_manaCost');
        $this->addSql('CREATE TABLE face_manaCost (card_id VARCHAR(36) NOT NULL, face_index INTEGER NOT NULL, symbol_id VARCHAR(20) NOT NULL, PRIMARY KEY(card_id, face_index, symbol_id))');
        $this->addSql('INSERT INTO face_manaCost (card_id, face_index, symbol_id) SELECT card_id, face_index, symbol_id FROM __temp__face_manaCost');
        $this->addSql('DROP TABLE __temp__face_manaCost');
        $this->addSql('CREATE INDEX IDX_2855F4FE4ACC9A2048086782 ON face_manaCost (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_2855F4FEC0F75674 ON face_manaCost (symbol_id)');
        $this->addSql('DROP INDEX IDX_D01563FC5787671D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__setOfCard AS SELECT code, name, released_date, icon_url, icon_local, setType_id FROM setOfCard');
        $this->addSql('DROP TABLE setOfCard');
        $this->addSql('CREATE TABLE setOfCard (code VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, released_date DATE DEFAULT NULL, icon_url VARCHAR(255) NOT NULL, icon_local VARCHAR(255) DEFAULT NULL, setType_id VARCHAR(50) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('INSERT INTO setOfCard (code, name, released_date, icon_url, icon_local, setType_id) SELECT code, name, released_date, icon_url, icon_local, setType_id FROM __temp__setOfCard');
        $this->addSql('DROP TABLE __temp__setOfCard');
        $this->addSql('CREATE INDEX IDX_D01563FC5787671D ON setOfCard (setType_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__symbol AS SELECT code, name, is_funny, is_mana, icon_url, cmc, icon_local FROM symbol');
        $this->addSql('DROP TABLE symbol');
        $this->addSql('CREATE TABLE symbol (code VARCHAR(20) NOT NULL, name VARCHAR(100) NOT NULL, is_funny BOOLEAN NOT NULL, is_mana BOOLEAN NOT NULL, icon_url VARCHAR(255) DEFAULT NULL, cmc NUMERIC(10, 2) DEFAULT NULL, icon_local VARCHAR(255) DEFAULT NULL, PRIMARY KEY(code))');
        $this->addSql('INSERT INTO symbol (code, name, is_funny, is_mana, icon_url, cmc, icon_local) SELECT code, name, is_funny, is_mana, icon_url, cmc, icon_local FROM __temp__symbol');
        $this->addSql('DROP TABLE __temp__symbol');
        $this->addSql('DROP INDEX IDX_E3934EAFC0F75674');
        $this->addSql('DROP INDEX IDX_E3934EAF7ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__symbol_color AS SELECT symbol_id, color_id FROM symbol_color');
        $this->addSql('DROP TABLE symbol_color');
        $this->addSql('CREATE TABLE symbol_color (symbol_id VARCHAR(20) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(symbol_id, color_id))');
        $this->addSql('INSERT INTO symbol_color (symbol_id, color_id) SELECT symbol_id, color_id FROM __temp__symbol_color');
        $this->addSql('DROP TABLE __temp__symbol_color');
        $this->addSql('CREATE INDEX IDX_E3934EAFC0F75674 ON symbol_color (symbol_id)');
        $this->addSql('CREATE INDEX IDX_E3934EAF7ADA1FB5 ON symbol_color (color_id)');
    }
}

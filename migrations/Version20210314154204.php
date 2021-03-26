<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210314154204 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_161498D310FB0D18');
        $this->addSql('DROP INDEX IDX_161498D3F3747573');
        $this->addSql('DROP INDEX IDX_161498D38C22AA1A');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card AS SELECT id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date FROM card');
        $this->addSql('DROP TABLE card');
        $this->addSql('CREATE TABLE card (id_scryfall VARCHAR(36) NOT NULL COLLATE BINARY, layout_id VARCHAR(50) NOT NULL COLLATE BINARY, rarity_id VARCHAR(50) NOT NULL COLLATE BINARY, set_id VARCHAR(10) NOT NULL COLLATE BINARY, id_oracle VARCHAR(36) NOT NULL COLLATE BINARY, id_arena INTEGER DEFAULT NULL, released_date DATE DEFAULT NULL, PRIMARY KEY(id_scryfall), CONSTRAINT FK_161498D38C22AA1A FOREIGN KEY (layout_id) REFERENCES layout (code) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_161498D3F3747573 FOREIGN KEY (rarity_id) REFERENCES rarity (name) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_161498D310FB0D18 FOREIGN KEY (set_id) REFERENCES "set" (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card (id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date) SELECT id_scryfall, layout_id, rarity_id, set_id, id_oracle, id_arena, released_date FROM __temp__card');
        $this->addSql('DROP TABLE __temp__card');
        $this->addSql('CREATE INDEX IDX_161498D310FB0D18 ON card (set_id)');
        $this->addSql('CREATE INDEX IDX_161498D3F3747573 ON card (rarity_id)');
        $this->addSql('CREATE INDEX IDX_161498D38C22AA1A ON card (layout_id)');
        $this->addSql('DROP INDEX IDX_2A7FDD257ADA1FB5');
        $this->addSql('DROP INDEX IDX_2A7FDD254ACC9A20');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_ColorIdentity AS SELECT card_id, color_id FROM Card_ColorIdentity');
        $this->addSql('DROP TABLE Card_ColorIdentity');
        $this->addSql('CREATE TABLE Card_ColorIdentity (card_id VARCHAR(36) NOT NULL COLLATE BINARY, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, color_id), CONSTRAINT FK_2A7FDD254ACC9A20 FOREIGN KEY (card_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2A7FDD257ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Card_ColorIdentity (card_id, color_id) SELECT card_id, color_id FROM __temp__Card_ColorIdentity');
        $this->addSql('DROP TABLE __temp__Card_ColorIdentity');
        $this->addSql('CREATE INDEX IDX_2A7FDD257ADA1FB5 ON Card_ColorIdentity (color_id)');
        $this->addSql('CREATE INDEX IDX_2A7FDD254ACC9A20 ON Card_ColorIdentity (card_id)');
        $this->addSql('DROP INDEX IDX_75D8D4037ADA1FB5');
        $this->addSql('DROP INDEX IDX_75D8D4034ACC9A20');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_ProducedMana AS SELECT card_id, color_id FROM Card_ProducedMana');
        $this->addSql('DROP TABLE Card_ProducedMana');
        $this->addSql('CREATE TABLE Card_ProducedMana (card_id VARCHAR(36) NOT NULL COLLATE BINARY, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, color_id), CONSTRAINT FK_75D8D4034ACC9A20 FOREIGN KEY (card_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_75D8D4037ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Card_ProducedMana (card_id, color_id) SELECT card_id, color_id FROM __temp__Card_ProducedMana');
        $this->addSql('DROP TABLE __temp__Card_ProducedMana');
        $this->addSql('CREATE INDEX IDX_75D8D4037ADA1FB5 ON Card_ProducedMana (color_id)');
        $this->addSql('CREATE INDEX IDX_75D8D4034ACC9A20 ON Card_ProducedMana (card_id)');
        $this->addSql('DROP INDEX IDX_DA051929115D4552');
        $this->addSql('DROP INDEX IDX_DA0519294ACC9A20');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_Keyword AS SELECT card_id, keyword_id FROM Card_Keyword');
        $this->addSql('DROP TABLE Card_Keyword');
        $this->addSql('CREATE TABLE Card_Keyword (card_id VARCHAR(36) NOT NULL COLLATE BINARY, keyword_id VARCHAR(100) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, keyword_id), CONSTRAINT FK_DA0519294ACC9A20 FOREIGN KEY (card_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DA051929115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Card_Keyword (card_id, keyword_id) SELECT card_id, keyword_id FROM __temp__Card_Keyword');
        $this->addSql('DROP TABLE __temp__Card_Keyword');
        $this->addSql('CREATE INDEX IDX_DA051929115D4552 ON Card_Keyword (keyword_id)');
        $this->addSql('CREATE INDEX IDX_DA0519294ACC9A20 ON Card_Keyword (card_id)');
        $this->addSql('DROP INDEX IDX_E0C11882D08E3C99');
        $this->addSql('DROP INDEX IDX_E0C118824ACC9A20');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_Related AS SELECT card_id, relatedCard_id FROM Card_Related');
        $this->addSql('DROP TABLE Card_Related');
        $this->addSql('CREATE TABLE Card_Related (card_id VARCHAR(36) NOT NULL COLLATE BINARY, relatedCard_id VARCHAR(36) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, relatedCard_id), CONSTRAINT FK_E0C118824ACC9A20 FOREIGN KEY (card_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_E0C11882D08E3C99 FOREIGN KEY (relatedCard_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Card_Related (card_id, relatedCard_id) SELECT card_id, relatedCard_id FROM __temp__Card_Related');
        $this->addSql('DROP TABLE __temp__Card_Related');
        $this->addSql('CREATE INDEX IDX_E0C11882D08E3C99 ON Card_Related (relatedCard_id)');
        $this->addSql('CREATE INDEX IDX_E0C118824ACC9A20 ON Card_Related (card_id)');
        $this->addSql('DROP INDEX IDX_67066B3DF9031785');
        $this->addSql('DROP INDEX IDX_67066B3DE6E0AB93');
        $this->addSql('DROP INDEX IDX_67066B3D4ACC9A20');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_legality AS SELECT id, card_id, legalityType_id, legalityValue_id FROM card_legality');
        $this->addSql('DROP TABLE card_legality');
        $this->addSql('CREATE TABLE card_legality (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, card_id VARCHAR(36) NOT NULL COLLATE BINARY, legalityType_id VARCHAR(50) NOT NULL COLLATE BINARY, legalityValue_id VARCHAR(50) NOT NULL COLLATE BINARY, CONSTRAINT FK_67066B3D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67066B3DE6E0AB93 FOREIGN KEY (legalityType_id) REFERENCES legality_type (name) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_67066B3DF9031785 FOREIGN KEY (legalityValue_id) REFERENCES legality_value (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO card_legality (id, card_id, legalityType_id, legalityValue_id) SELECT id, card_id, legalityType_id, legalityValue_id FROM __temp__card_legality');
        $this->addSql('DROP TABLE __temp__card_legality');
        $this->addSql('CREATE INDEX IDX_67066B3DF9031785 ON card_legality (legalityValue_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DE6E0AB93 ON card_legality (legalityType_id)');
        $this->addSql('CREATE INDEX IDX_67066B3D4ACC9A20 ON card_legality (card_id)');
        $this->addSql('DROP INDEX IDX_5147B67B7970CF8');
        $this->addSql('DROP INDEX IDX_5147B674ACC9A20');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face AS SELECT "index", card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness FROM face');
        $this->addSql('DROP TABLE face');
        $this->addSql('CREATE TABLE face ("index" INTEGER NOT NULL, card_id VARCHAR(36) NOT NULL COLLATE BINARY, artist_id VARCHAR(255) DEFAULT NULL COLLATE BINARY, image_url VARCHAR(255) DEFAULT NULL COLLATE BINARY, name VARCHAR(255) NOT NULL COLLATE BINARY, type_line VARCHAR(100) NOT NULL COLLATE BINARY, oracle_text VARCHAR(2000) DEFAULT NULL COLLATE BINARY, printed_text VARCHAR(2000) DEFAULT NULL COLLATE BINARY, power VARCHAR(5) DEFAULT NULL COLLATE BINARY, toughness VARCHAR(5) DEFAULT NULL COLLATE BINARY, PRIMARY KEY(card_id, "index"), CONSTRAINT FK_5147B674ACC9A20 FOREIGN KEY (card_id) REFERENCES card (idScryfall) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5147B67B7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (name) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO face ("index", card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness) SELECT "index", card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness FROM __temp__face');
        $this->addSql('DROP TABLE __temp__face');
        $this->addSql('CREATE INDEX IDX_5147B67B7970CF8 ON face (artist_id)');
        $this->addSql('CREATE INDEX IDX_5147B674ACC9A20 ON face (card_id)');
        $this->addSql('DROP INDEX IDX_20B164BE7ADA1FB5');
        $this->addSql('DROP INDEX IDX_20B164BE4ACC9A2048086782');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Face_Color AS SELECT card_id, face_index, color_id FROM Face_Color');
        $this->addSql('DROP TABLE Face_Color');
        $this->addSql('CREATE TABLE Face_Color (card_id VARCHAR(36) NOT NULL COLLATE BINARY, face_index INTEGER NOT NULL, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, face_index, color_id), CONSTRAINT FK_20B164BE4ACC9A2048086782 FOREIGN KEY (card_id, face_index) REFERENCES face (card_id, "index") NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_20B164BE7ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Face_Color (card_id, face_index, color_id) SELECT card_id, face_index, color_id FROM __temp__Face_Color');
        $this->addSql('DROP TABLE __temp__Face_Color');
        $this->addSql('CREATE INDEX IDX_20B164BE7ADA1FB5 ON Face_Color (color_id)');
        $this->addSql('CREATE INDEX IDX_20B164BE4ACC9A2048086782 ON Face_Color (card_id, face_index)');
        $this->addSql('DROP INDEX IDX_625DDD5DC0F75674');
        $this->addSql('DROP INDEX IDX_625DDD5D4ACC9A2048086782');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Face_ManaCost AS SELECT card_id, face_index, symbol_id FROM Face_ManaCost');
        $this->addSql('DROP TABLE Face_ManaCost');
        $this->addSql('CREATE TABLE Face_ManaCost (card_id VARCHAR(36) NOT NULL COLLATE BINARY, face_index INTEGER NOT NULL, symbol_id VARCHAR(20) NOT NULL COLLATE BINARY, PRIMARY KEY(card_id, face_index, symbol_id), CONSTRAINT FK_625DDD5D4ACC9A2048086782 FOREIGN KEY (card_id, face_index) REFERENCES face (card_id, "index") NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_625DDD5DC0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Face_ManaCost (card_id, face_index, symbol_id) SELECT card_id, face_index, symbol_id FROM __temp__Face_ManaCost');
        $this->addSql('DROP TABLE __temp__Face_ManaCost');
        $this->addSql('CREATE INDEX IDX_625DDD5DC0F75674 ON Face_ManaCost (symbol_id)');
        $this->addSql('CREATE INDEX IDX_625DDD5D4ACC9A2048086782 ON Face_ManaCost (card_id, face_index)');
        $this->addSql('ALTER TABLE rarity ADD COLUMN index_value INTEGER DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_E61425DC5787671D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__set AS SELECT code, name, released_date, setType_id FROM "set"');
        $this->addSql('DROP TABLE "set"');
        $this->addSql('CREATE TABLE "set" (code VARCHAR(10) NOT NULL COLLATE BINARY, name VARCHAR(100) NOT NULL COLLATE BINARY, released_date DATE DEFAULT NULL, setType_id VARCHAR(50) NOT NULL COLLATE BINARY, PRIMARY KEY(code), CONSTRAINT FK_E61425DC5787671D FOREIGN KEY (setType_id) REFERENCES set_type (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO "set" (code, name, released_date, setType_id) SELECT code, name, released_date, setType_id FROM __temp__set');
        $this->addSql('DROP TABLE __temp__set');
        $this->addSql('CREATE INDEX IDX_E61425DC5787671D ON "set" (setType_id)');
        $this->addSql('DROP INDEX IDX_3A63BA537ADA1FB5');
        $this->addSql('DROP INDEX IDX_3A63BA53C0F75674');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Symbol_Color AS SELECT symbol_id, color_id FROM Symbol_Color');
        $this->addSql('DROP TABLE Symbol_Color');
        $this->addSql('CREATE TABLE Symbol_Color (symbol_id VARCHAR(20) NOT NULL COLLATE BINARY, color_id VARCHAR(1) NOT NULL COLLATE BINARY, PRIMARY KEY(symbol_id, color_id), CONSTRAINT FK_3A63BA53C0F75674 FOREIGN KEY (symbol_id) REFERENCES symbol (code) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3A63BA537ADA1FB5 FOREIGN KEY (color_id) REFERENCES color (code) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO Symbol_Color (symbol_id, color_id) SELECT symbol_id, color_id FROM __temp__Symbol_Color');
        $this->addSql('DROP TABLE __temp__Symbol_Color');
        $this->addSql('CREATE INDEX IDX_3A63BA537ADA1FB5 ON Symbol_Color (color_id)');
        $this->addSql('CREATE INDEX IDX_3A63BA53C0F75674 ON Symbol_Color (symbol_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_2A7FDD254ACC9A20');
        $this->addSql('DROP INDEX IDX_2A7FDD257ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_ColorIdentity AS SELECT card_id, color_id FROM Card_ColorIdentity');
        $this->addSql('DROP TABLE Card_ColorIdentity');
        $this->addSql('CREATE TABLE Card_ColorIdentity (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, color_id))');
        $this->addSql('INSERT INTO Card_ColorIdentity (card_id, color_id) SELECT card_id, color_id FROM __temp__Card_ColorIdentity');
        $this->addSql('DROP TABLE __temp__Card_ColorIdentity');
        $this->addSql('CREATE INDEX IDX_2A7FDD254ACC9A20 ON Card_ColorIdentity (card_id)');
        $this->addSql('CREATE INDEX IDX_2A7FDD257ADA1FB5 ON Card_ColorIdentity (color_id)');
        $this->addSql('DROP INDEX IDX_DA0519294ACC9A20');
        $this->addSql('DROP INDEX IDX_DA051929115D4552');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_Keyword AS SELECT card_id, keyword_id FROM Card_Keyword');
        $this->addSql('DROP TABLE Card_Keyword');
        $this->addSql('CREATE TABLE Card_Keyword (card_id VARCHAR(36) NOT NULL, keyword_id VARCHAR(100) NOT NULL, PRIMARY KEY(card_id, keyword_id))');
        $this->addSql('INSERT INTO Card_Keyword (card_id, keyword_id) SELECT card_id, keyword_id FROM __temp__Card_Keyword');
        $this->addSql('DROP TABLE __temp__Card_Keyword');
        $this->addSql('CREATE INDEX IDX_DA0519294ACC9A20 ON Card_Keyword (card_id)');
        $this->addSql('CREATE INDEX IDX_DA051929115D4552 ON Card_Keyword (keyword_id)');
        $this->addSql('DROP INDEX IDX_75D8D4034ACC9A20');
        $this->addSql('DROP INDEX IDX_75D8D4037ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_ProducedMana AS SELECT card_id, color_id FROM Card_ProducedMana');
        $this->addSql('DROP TABLE Card_ProducedMana');
        $this->addSql('CREATE TABLE Card_ProducedMana (card_id VARCHAR(36) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, color_id))');
        $this->addSql('INSERT INTO Card_ProducedMana (card_id, color_id) SELECT card_id, color_id FROM __temp__Card_ProducedMana');
        $this->addSql('DROP TABLE __temp__Card_ProducedMana');
        $this->addSql('CREATE INDEX IDX_75D8D4034ACC9A20 ON Card_ProducedMana (card_id)');
        $this->addSql('CREATE INDEX IDX_75D8D4037ADA1FB5 ON Card_ProducedMana (color_id)');
        $this->addSql('DROP INDEX IDX_E0C118824ACC9A20');
        $this->addSql('DROP INDEX IDX_E0C11882D08E3C99');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Card_Related AS SELECT card_id, relatedCard_id FROM Card_Related');
        $this->addSql('DROP TABLE Card_Related');
        $this->addSql('CREATE TABLE Card_Related (card_id VARCHAR(36) NOT NULL, relatedCard_id VARCHAR(36) NOT NULL, PRIMARY KEY(card_id, relatedCard_id))');
        $this->addSql('INSERT INTO Card_Related (card_id, relatedCard_id) SELECT card_id, relatedCard_id FROM __temp__Card_Related');
        $this->addSql('DROP TABLE __temp__Card_Related');
        $this->addSql('CREATE INDEX IDX_E0C118824ACC9A20 ON Card_Related (card_id)');
        $this->addSql('CREATE INDEX IDX_E0C11882D08E3C99 ON Card_Related (relatedCard_id)');
        $this->addSql('DROP INDEX IDX_20B164BE4ACC9A2048086782');
        $this->addSql('DROP INDEX IDX_20B164BE7ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Face_Color AS SELECT card_id, face_index, color_id FROM Face_Color');
        $this->addSql('DROP TABLE Face_Color');
        $this->addSql('CREATE TABLE Face_Color (card_id VARCHAR(36) NOT NULL, face_index INTEGER NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(card_id, face_index, color_id))');
        $this->addSql('INSERT INTO Face_Color (card_id, face_index, color_id) SELECT card_id, face_index, color_id FROM __temp__Face_Color');
        $this->addSql('DROP TABLE __temp__Face_Color');
        $this->addSql('CREATE INDEX IDX_20B164BE4ACC9A2048086782 ON Face_Color (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_20B164BE7ADA1FB5 ON Face_Color (color_id)');
        $this->addSql('DROP INDEX IDX_625DDD5D4ACC9A2048086782');
        $this->addSql('DROP INDEX IDX_625DDD5DC0F75674');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Face_ManaCost AS SELECT card_id, face_index, symbol_id FROM Face_ManaCost');
        $this->addSql('DROP TABLE Face_ManaCost');
        $this->addSql('CREATE TABLE Face_ManaCost (card_id VARCHAR(36) NOT NULL, face_index INTEGER NOT NULL, symbol_id VARCHAR(20) NOT NULL, PRIMARY KEY(card_id, face_index, symbol_id))');
        $this->addSql('INSERT INTO Face_ManaCost (card_id, face_index, symbol_id) SELECT card_id, face_index, symbol_id FROM __temp__Face_ManaCost');
        $this->addSql('DROP TABLE __temp__Face_ManaCost');
        $this->addSql('CREATE INDEX IDX_625DDD5D4ACC9A2048086782 ON Face_ManaCost (card_id, face_index)');
        $this->addSql('CREATE INDEX IDX_625DDD5DC0F75674 ON Face_ManaCost (symbol_id)');
        $this->addSql('DROP INDEX IDX_3A63BA53C0F75674');
        $this->addSql('DROP INDEX IDX_3A63BA537ADA1FB5');
        $this->addSql('CREATE TEMPORARY TABLE __temp__Symbol_Color AS SELECT symbol_id, color_id FROM Symbol_Color');
        $this->addSql('DROP TABLE Symbol_Color');
        $this->addSql('CREATE TABLE Symbol_Color (symbol_id VARCHAR(20) NOT NULL, color_id VARCHAR(1) NOT NULL, PRIMARY KEY(symbol_id, color_id))');
        $this->addSql('INSERT INTO Symbol_Color (symbol_id, color_id) SELECT symbol_id, color_id FROM __temp__Symbol_Color');
        $this->addSql('DROP TABLE __temp__Symbol_Color');
        $this->addSql('CREATE INDEX IDX_3A63BA53C0F75674 ON Symbol_Color (symbol_id)');
        $this->addSql('CREATE INDEX IDX_3A63BA537ADA1FB5 ON Symbol_Color (color_id)');
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
        $this->addSql('DROP INDEX IDX_67066B3D4ACC9A20');
        $this->addSql('DROP INDEX IDX_67066B3DE6E0AB93');
        $this->addSql('DROP INDEX IDX_67066B3DF9031785');
        $this->addSql('CREATE TEMPORARY TABLE __temp__card_legality AS SELECT id, card_id, legalityType_id, legalityValue_id FROM card_legality');
        $this->addSql('DROP TABLE card_legality');
        $this->addSql('CREATE TABLE card_legality (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, card_id VARCHAR(36) NOT NULL, legalityType_id VARCHAR(50) NOT NULL, legalityValue_id VARCHAR(50) NOT NULL)');
        $this->addSql('INSERT INTO card_legality (id, card_id, legalityType_id, legalityValue_id) SELECT id, card_id, legalityType_id, legalityValue_id FROM __temp__card_legality');
        $this->addSql('DROP TABLE __temp__card_legality');
        $this->addSql('CREATE INDEX IDX_67066B3D4ACC9A20 ON card_legality (card_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DE6E0AB93 ON card_legality (legalityType_id)');
        $this->addSql('CREATE INDEX IDX_67066B3DF9031785 ON card_legality (legalityValue_id)');
        $this->addSql('DROP INDEX IDX_5147B674ACC9A20');
        $this->addSql('DROP INDEX IDX_5147B67B7970CF8');
        $this->addSql('CREATE TEMPORARY TABLE __temp__face AS SELECT "index", card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness FROM face');
        $this->addSql('DROP TABLE face');
        $this->addSql('CREATE TABLE face ("index" INTEGER NOT NULL, card_id VARCHAR(36) NOT NULL, artist_id VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, type_line VARCHAR(100) NOT NULL, oracle_text VARCHAR(2000) DEFAULT NULL, printed_text VARCHAR(2000) DEFAULT NULL, power VARCHAR(5) DEFAULT NULL, toughness VARCHAR(5) DEFAULT NULL, PRIMARY KEY(card_id, "index"))');
        $this->addSql('INSERT INTO face ("index", card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness) SELECT "index", card_id, artist_id, image_url, name, type_line, oracle_text, printed_text, power, toughness FROM __temp__face');
        $this->addSql('DROP TABLE __temp__face');
        $this->addSql('CREATE INDEX IDX_5147B674ACC9A20 ON face (card_id)');
        $this->addSql('CREATE INDEX IDX_5147B67B7970CF8 ON face (artist_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__rarity AS SELECT name, description FROM rarity');
        $this->addSql('DROP TABLE rarity');
        $this->addSql('CREATE TABLE rarity (name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(name))');
        $this->addSql('INSERT INTO rarity (name, description) SELECT name, description FROM __temp__rarity');
        $this->addSql('DROP TABLE __temp__rarity');
        $this->addSql('DROP INDEX IDX_E61425DC5787671D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__set AS SELECT code, name, released_date, setType_id FROM "set"');
        $this->addSql('DROP TABLE "set"');
        $this->addSql('CREATE TABLE "set" (code VARCHAR(10) NOT NULL, name VARCHAR(100) NOT NULL, released_date DATE DEFAULT NULL, setType_id VARCHAR(50) NOT NULL, PRIMARY KEY(code))');
        $this->addSql('INSERT INTO "set" (code, name, released_date, setType_id) SELECT code, name, released_date, setType_id FROM __temp__set');
        $this->addSql('DROP TABLE __temp__set');
        $this->addSql('CREATE INDEX IDX_E61425DC5787671D ON "set" (setType_id)');
    }
}

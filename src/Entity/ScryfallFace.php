<?php

namespace App\Entity;

use App\Entity\ScryfallAbstract;

/**
 * scryfall json data for card's faces
 */
class ScryfallFace extends ScryfallAbstract {
    protected function subObjectMapping(): array {
        return [];
    }
    // card-face-objects
    public ?string $artist;
    public ?string $artist_id;
    public ?array $color_indicator;
    public ?array $colors;
    public ?string $flavor_text;
    public ?string $illustration_id;
    public ?array $image_uris;
    public ?string $loyalty;
    public string $mana_cost;
    public string $name;
    public string $object;
    public ?string $oracle_text;
    public ?string $power;
    public ?string $printed_name;
    public ?string $printed_text;
    public ?string $printed_type_line;
    public ?string $toughness;
    public string $type_line;
    public ?string $watermark;
}

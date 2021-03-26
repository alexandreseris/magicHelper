<?php

namespace App\Entity;

use App\Entity\ScryfallAbstract;

class ScryfallCard extends ScryfallAbstract {
    protected function subObjectMapping(): array {
        return [
            "card_faces" => "App\\Entity\\ScryfallFace",
            "all_parts" => "App\\Entity\\ScryfallRelated"
        ];
    }
    // core-card-fields
    public ?int $arena_id;
    public string $id;
    public string $lang;
    public ?int $mtgo_id;
    public ?int $mtgo_foil_id;
    public ?array $multiverse_ids;
    public ?int $tcgplayer_id;
    public ?int $cardmarket_id;
    public string $object;
    public string $oracle_id;
    public string $prints_search_uri;
    public string $rulings_uri;
    public string $scryfall_uri;
    public string $uri;
    // gameplay-fields
    public ?array $all_parts;
    public ?array $card_faces;
    public float $cmc;
    public array $color_identity;
    public ?array $color_indicator;
    public ?array $colors;
    public ?int $edhrec_rank;
    public bool $foil;
    public ?string $hand_modifier;
    public array $keywords;
    public string $layout;
    public array $legalities;
    public ?string $life_modifier;
    public ?string $loyalty;
    public ?string $mana_cost;
    public string $name;
    public bool $nonfoil;
    public ?string $oracle_text;
    public bool $oversized;
    public ?string $power;
    public ?array $produced_mana;
    public bool $reserved;
    public ?string $toughness;
    public string $type_line;
    // print-fields
    public ?string $artist;
    public ?array $artist_ids;
    public bool $booster;
    public string $border_color;
    public string $card_back_id;
    public string $collector_number;
    public ?bool $content_warning;
    public bool $digital;
    public ?string $flavor_name;
    public ?string $flavor_text;
    public ?array $frame_effects;
    public string $frame;
    public bool $full_art;
    public array $games;
    public bool $highres_image;
    public ?string $illustration_id;
    public string $image_status;
    public ?array $image_uris;
    public array $prices;
    public ?string $printed_name;
    public ?string $printed_text;
    public ?string $printed_type_line;
    public bool $promo;
    public ?array $promo_types;
    public ?array $purchase_uris;
    public string $rarity;
    public array $related_uris;
    public string $released_at;
    public bool $reprint;
    public string $scryfall_set_uri;
    public string $set_name;
    public string $set_search_uri;
    public string $set_type;
    public string $set_uri;
    public string $set;
    public bool $story_spotlight;
    public bool $textless;
    public bool $variation;
    public ?string $variation_of;
    public ?string $watermark;
    public ?array $preview;
}

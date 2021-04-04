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

    /**
     * return an array usable with ScryfallFace constructor from a ScryfallCard obj
     * @param \App\Entity\ScryfallCard $cardObject the input card object to "translate"
     */
    public static function faceArrayFromCardObject(\App\Entity\ScryfallCard $cardObject): array {
        // by default properties are mapped by name.
        // if specific mapping is needed, it should be setup here
        $faceArray = [
            "object" => "card_face",
            "artist_id" => is_null($cardObject->artist_ids) ? null : $cardObject->artist_ids[0],
        ];
        $reflect = new \ReflectionClass(self::class);
        foreach (
            array_map(
                function($value) {return $value->getName();},
                $reflect->getProperties()
            )
            as $prop)
        {
            if ( ! key_exists($prop, $faceArray) ) {
                $faceArray[$prop] = $cardObject->{$prop};
            }
        }
        return $faceArray;
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

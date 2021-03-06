<?php

namespace App\Entity;

use App\Entity\ScryfallAbstract;

/**
 * scryfall json data for card's related cards
 */
class ScryfallRelated extends ScryfallAbstract {
    protected function subObjectMapping(): array {
        return [];
    }
    // related-card-objects
    public string $id;
    public string $object;
    public string $component;
    public string $name;
    public string $type_line;
    public string $uri;
}

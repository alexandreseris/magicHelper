<?php

namespace App\Entity;

use Exception;

/**
 * This class and all class inheriting from it represents json data from scryfall.
 * Theses classes are used to ensure control over scryfall schema changes (currently on the card context only).
 * If a property is added, removed or changed, the update process will throw an exception to inform schema has changed and should be updated.
 * The classes defined this way can be generated with the commande scryfall:schema.
 * note: theses classes should not be used with doctrine as they only represent json data from scryfall.
 */
abstract class ScryfallAbstract {
    public function __construct(array $jsonObj) {
        foreach ($jsonObj as $prop => $value) {
            if (property_exists($this, $prop)) {
                if ( in_array($prop, array_keys($this->subObjectMapping()) ) ) {
                    $this->$prop = [];
                    $targetClass = $this->subObjectMapping()[$prop];
                    foreach ($value as $subProp => $subValue) {
                        $this->$prop[$subProp] = new $targetClass($subValue);
                    }
                } else {
                    $this->$prop = $value;
                }
            } else {
                throw new \Exception($prop . " property undefined on class " . static::class . "(value associated: " . print_r($value, true) . "). you should probably update the class definition using API documentation", 1);
            }
        }
        // every prop is not present on the json, so the one missing are init to null
        $reflect = new \ReflectionClass($this);
        foreach (array_map(function($value) {return $value->getName();}, $reflect->getProperties()) as $prop) {
            $reflectProp = new \ReflectionProperty(static::class, $prop);
            if (! $reflectProp->isInitialized($this)) {
                if ($reflectProp->getType()->allowsNull()) {
                    $this->$prop = null;
                } else {
                    throw new \Exception("missing property " . $prop . " and property is not nullable on the class " . static::class . ". You should updated the class definition.\n" . 
                        "detailled obj:\n" .
                        print_r($jsonObj, true), 1);
                }
            }
        }
    }

    // define the mapping for properties that should be list of array of specific class
    abstract protected function subObjectMapping(): array;
}
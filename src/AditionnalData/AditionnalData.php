<?php

namespace App\AditionnalData;

class AditionnalData {

    public $formats = [
        "standard" => ["description" => "The Standard format uses the most recently released Magic sets."],
        "future" => ["description" => ""],
        "historic" => ["description" => "Historic was officially announced as a format in a video and accompanying article on June 27, 2019. It was created as \"an MTG Arena-first format\" as way for players to use cards that are available on Arena, but are not currently legal in the standard format due to rotation, ban, or other reasons. The three ways that cards join the historic format are: appearing in a standard-legal set, appearing in supplemental sets released on Arena (the non-standard set Jumpstart being an example), or added via 15-20 card sets called Historic Anthologies. Like other constructed formats, Historic maintains its own banned list."],
        "gladiator" => ["description" => "Introduced during the COVID-19 pandemic and the cessation of live events, Gladiator is a casual constructed, singleton format that is specific to Magic the Gathering Arena."],
        "pioneer" => ["description" => "Nonrotating format featuring cards from Return to Ravnica and forward."],
        "modern" => ["description" => "Cards from Core Set Eighth Edition and Mirrodin through today are legal in this format. Again, decks require a minimum of sixty cards and may have a sideboard of up to fifteen cards."],
        "legacy" => ["description" => "Allows cards from all legal sets, but bans certain cards for power level reasons."],
        "pauper" => ["description" => "Restricts decks to only cards with the common rarity."],
        "vintage" => ["description" => "The most powerful of constructed formats, this format allows the “Power Nine” to be played."],
        "penny" => ["description" => "An unofficial Magic Online budget format where the legality rules include only cards that cost 0.02 ticket - roughly one penny."],
        "commander" => ["description" => "Each player chooses a legendary creature to be their “commander” and makes a 99-card deck around that creature."],
        "brawl" => ["description" => "Choose your champion! Brawl is a little like Standard, a little like Commander, and a uniquely exciting deck-brewing challenge."],
        "duel" => ["description" => ""],
        "oldschool" => ["description" => "A format where only cards that were printed in 1993 and 1994 (the first 2 years of Magic) are allowed. There are many different variations, often with different rules set regionally by a playgroup or a local tournament organizer."],
        "premodern" => ["description" => "Premodern is a community-created constructed format consisting of the sets from Fourth Edition to Scourge."],
    ];

    public $legalities = [
        "not_legal" => ["description" => "The card is excluded because the format does not permit any sets that the card was printed in. For example, the card may be too old for Modern format while legal in Legacy."],
        "legal" => ["description" => "The card is allowed in this format."],
        "banned" => ["description" => "If a card appears on the banned list for your chosen format, then you may not include that card in your deck or sideboard. Doing so makes your deck illegal to play in any sanctioned tournaments for that format."],
        "restricted" => ["description" => "If you wish to use a card that’s on the restricted list for your chosen format, you may include only a single copy of that card, counting both your main deck and your sideboard."]
    ];

    public $rarities = [
        "common" => ["index_value" => 0, "color" => null],
        "uncommon" => ["index_value" => 1, "color" => "#474e51"],
        "rare" => ["index_value" => 2, "color" => "#83703d"],
        "mythic" => ["index_value" => 3, "color" => "#b02911"],
        "special" => ["index_value" => 4, "color" => null],
        "bonus" => ["index_value" => 5, "color" => null],
    ];    
}

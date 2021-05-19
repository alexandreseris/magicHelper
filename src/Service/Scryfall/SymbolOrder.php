<?php

namespace App\Service\Scryfall;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SymbolOrder
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private array $colorList;
    private array $standardManaList;
    private array $variableManaList;
    private array $genericManaListAditionnal;
    private array $multiManaGenericManaList;
    private array $multiNotManaSymbol;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->variableManaList = ['X', 'Y', 'Z'];
        $this->genericManaListAditionnal = ['P', 'S', '½', '∞'];
        $this->multiManaGenericManaList = ['2'];
        $this->multiNotManaSymbol = ['P'];
        $this->colorList = ['W', 'U', 'B', 'R', 'G'];
        $this->standardManaList = array_merge(['C'], $this->colorList);
    }

    /**
     * compute information used to classify symbols and return these as associative array used to sort symbols
     * @param \App\Entity\Symbol $symbol the symbol you wish to analyse
     */
    private function computeSymbolKeys(\App\Entity\Symbol $symbol) : array {
        $cleanedSymbol = $symbol->getCode();
        $cleanedSymbol = str_replace('{', '', $cleanedSymbol);
        $cleanedSymbol = str_replace('}', '', $cleanedSymbol);
        $regMatches = [];
        $keys = [
            "isVariableGenericMana" => -1,
            "isNumber" => -1,
            "number" => null,
            "isGenericAditionnal" => -1,
            "isMultiWithGen" => -1,
            "isMultiWithNoMana" => -1,
            "isMultiFullColor" => -1,
            "isStandardMana" => -1,
            "isHalfMana" => -1,
            "symbolIndexes" => []
        ];
        if (in_array($cleanedSymbol, $this->variableManaList)) {
            $keys["isVariableGenericMana"] = 1;
            $keys["symbolIndexes"][] = array_search($cleanedSymbol, $this->variableManaList);
        }
        else if (preg_match('/^\d+$/', $cleanedSymbol)) {
            $keys["isNumber"] = 1;
            $keys["number"] = intval($cleanedSymbol);
        }
        else if (in_array($cleanedSymbol, $this->genericManaListAditionnal)) {
            $keys["isGenericAditionnal"] = 1;
            $keys["symbolIndexes"][] = array_search($cleanedSymbol, $this->genericManaListAditionnal);
        }
        else if (str_contains($cleanedSymbol, '/')) {
            foreach (explode('/', $cleanedSymbol) as $subsymbol) {
                if (in_array($subsymbol, $this->multiManaGenericManaList)) {
                    $keys["isMultiWithGen"] = 1;
                } else if (in_array($subsymbol, $this->multiNotManaSymbol)) {
                    $keys["isMultiWithNoMana"] = 1;
                } else if (in_array($subsymbol, $this->standardManaList)) {
                    $keys["symbolIndexes"][] = array_search($subsymbol, $this->standardManaList);
                }
            }
            if ($keys["isMultiWithGen"] === -1 && $keys["isMultiWithNoMana"] === -1) {
                $keys["isMultiFullColor"] = 1;
            }
        }
        else if (in_array($cleanedSymbol, $this->standardManaList)) {
            $keys["isStandardMana"] = 1;
            $keys["symbolIndexes"][] = array_search($cleanedSymbol, $this->standardManaList);
        }
        else if (preg_match(
            '/^H(' . implode('|', $this->colorList) . ')$/',
            $cleanedSymbol,
            $regMatches)
        ) {
            $keys["isHalfMana"] = 1;
            $keys["symbolIndexes"][] = array_search($regMatches[1], $this->standardManaList);
        }
        return $keys;
    }

    /**
     * the callable used to sort symbols. The first appearing symbol has the lowest index
     * @param \App\Entity\Symbol $firstItem
     * @param \App\Entity\Symbol $nextItem
     */
    private function sortItems(\App\Entity\Symbol $firstItem, \App\Entity\Symbol $nextItem) {
        $firstItemKeys = $this->computeSymbolKeys($firstItem);
        $nextItemKeys = $this->computeSymbolKeys($nextItem);
        foreach ($firstItemKeys as $keyName => $firstItemValue) {
            $nextItemValue = $nextItemKeys[$keyName];
            if (str_starts_with($keyName, 'is') && $nextItemValue !== $firstItemValue) {
                return $nextItemKeys[$keyName];
            } else if ($keyName === 'number' && (! is_null($firstItemValue) || ! is_null($nextItemValue))) {
                if (is_null($nextItemValue) || $firstItemValue > $nextItemValue) {
                    return -1;
                } else {
                    return 1;
                }
            } else if ($keyName === 'symbolIndexes') {
                // we can consider the arrays will always be the same size between first and next item
                // because the function would return earlier if thats not the case
                foreach ($firstItemValue as $ind => $firstItemSymbolIndex) {
                    $nextItemSymbolIndex = $nextItemValue[$ind];
                    if ($firstItemSymbolIndex > $nextItemSymbolIndex) {
                        return -1;
                    } else {
                        return 1;
                    }
                }
            }
        }
    }

    /**
     * main function of this service, update the symbols with a computed order to mimic the official order
     */
    public function sortSymbols() {
        $symbols = $this->entityManager->getRepository(\App\Entity\Symbol::class)->findBy(["is_mana" => true]);

        $this->logger->debug("sortSymbols call, before sorting, symbols are :");
        $this->logger->debug(print_r(array_map(function($symbol) {return $symbol->getCode();}, $symbols), true));

        $this->logger->debug("beginning sorting");
        usort($symbols, [$this, 'sortItems']);

        $this->logger->debug("sortSymbols call, after sorting, symbols are :");
        $this->logger->debug(print_r(array_map(function($symbol) {return $symbol->getCode();}, $symbols), true));

        /** @var \App\Entity\Symbol $symbol */
        foreach ($symbols as $ind => $symbol) {
            $symbol->setIndexValue($ind);
        }
        $this->entityManager->flush();
    }
}
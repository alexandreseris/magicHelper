<?php

namespace App\Service\Scryfall;

use App\Service\Crawler;

class Schema
{
    private string $scryfallApiDocumentationUrl;

    private Crawler $htmlparser;

    public function __construct(
        string $scryfallApiDocumentationUrl,
        Crawler $htmlparser
    ) {
        $this->$scryfallApiDocumentationUrl = $scryfallApiDocumentationUrl;
        $this->htmlparser = $htmlparser;
    }

    /**
     * build properties of card object based on the api documentation.
     * Used manually for updating/creating classes representing card and associated from the scryfall perspective
     * this methode is not 100% accurate as it depends on the reliability of the scryfall doc. you may need to update classes by yourself :|.
     */
    public function getCardSchemaFromDoc(): string
    {
        $documentationTypeMapping = [
            'Integer' => 'int',
            'UUID' => 'string',
            'String' => 'string',
            'Array' => 'array',
            'URI' => 'string',
            'Decimal' => 'float',
            'Colors' => 'array',
            'Boolean' => 'bool',
            'Object' => 'array',
            'Date' => 'string',
        ];
        $response = $this->httpclient->request('GET', $this->scryfallApiDocumentationUrl . '/cards');
        $responseContent = $response->getContent();
        /** @var Crawler $crawler */
        $crawler = new $this->htmlparser($responseContent);
        $mainSection = $crawler->filter('.prose')->eq(0);
        $dataTableNodes = $mainSection->filter('table tbody'); // properties tables (name, type and nullable)
        $titleTable = $mainSection->filter('h2')->each( // title of the properties tables
            function (Crawler $node, $i) {
                return $node->attr('id');
            }
        );
        $titleTable = array_slice($titleTable, count($titleTable) - $dataTableNodes->count()); // remove titles with no table attached. Depends on the fact that only the firsts titles have no table bellow
        $dataTable = $dataTableNodes->each(
            function (Crawler $tableNode, $i) use ($titleTable, $documentationTypeMapping) {
                $propertiesLines = $tableNode->filter('tr')->each(
                    function (Crawler $lineNode, $i) use ($documentationTypeMapping) {
                        $propertyName = $lineNode->filter('td code')->eq(0)->text();
                        $propertyType = $lineNode->filter('td')->eq(1)->text();
                        $propertyNullable = $lineNode->filter('td span')->count();
                        $nullable = false;
                        if ($propertyNullable > 0) {
                            $nullable = true;
                        }

                        return [$propertyName, $documentationTypeMapping[$propertyType], $nullable];
                    }
                );

                return [$titleTable[$i], $propertiesLines];
            }
        );
        // cleaning some properties
        foreach ($dataTable as &$dataType) {
            $typeLib = $dataType[0];
            $props = &$dataType[1];
            foreach ($props as &$prop) {
                $propName = $prop[0];
                $propType = $prop[1];
                $propNullable = $prop[2];
                if (str_contains($propName, '.')) {
                    $newNameProp = explode('.', $propName)[0];
                    $newProp = [$newNameProp, 'array', $propNullable];
                    if (!in_array($newProp, $props)) {
                        $props[] = $newProp;
                    }
                    unset($props[array_search($prop, $props)]);
                }
            }
            unset($prop);
        }
        unset($dataType, $prop, $props);

        $strBuild = '';
        foreach ($dataTable as $dataType) {
            $typeLib = $dataType[0];
            $props = $dataType[1];
            $strBuild = $strBuild.'// '.$typeLib."\n";
            foreach ($props as $prop) {
                $propName = $prop[0];
                $propType = $prop[1];
                $propNullable = $prop[2];
                $strBuild = $strBuild.'public ';
                if ($propNullable) {
                    $strBuild = $strBuild.'?';
                }
                $strBuild = $strBuild.$propType.' $'.$propName.";\n";
            }
        }

        return $strBuild;
    }
}
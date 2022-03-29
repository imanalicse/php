<?php
namespace App\Utils;

class DomFunctions
{
    /*
     * There is a problem with saveHTML() and saveXML(), both of them do not work correctly in Unix.
     * They do not save UTF-8 characters correctly when used in Unix, but they work in Windows.
     * So we need to do as follows. @see https://stackoverflow.com/a/20675396/1710782
     * $this->addRelNoFollow($event->event_description)
     * */
    public function addRelNoFollow($html, $whiteList = [], $newRels='nofollow noreferrer noopener')
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $a = $dom->getElementsByTagName('a');

        /** @var \DOMElement $anchor */
        foreach ($a as $anchor) {
            $href = $anchor->attributes->getNamedItem('href')->nodeValue;
            $domain = parse_url($href, PHP_URL_HOST);

            // Skip whiteList domains
            if (in_array($domain, $whiteList, true)) {
                continue;
            }

            // Check & get existing rel attribute values
            $rel = $anchor->attributes->getNamedItem('rel');
            if ($rel) {
                $values    = explode(' ', $rel->nodeValue);
                $noFollows = explode(' ', $newRels);

                $foundNew = false;
                foreach( (array)$noFollows as $relItem ){
                    if(!in_array($relItem, $values, true)) {
                        $foundNew = true;
                        $values[] = $relItem;
                    }
                }
                if(!$foundNew){
                    continue;
                }

                $newValue = implode(' ',$values);
            } else {
                $newValue = $newRels;
            }

            // Create new rel attribute
            $rel = $dom->createAttribute('rel');
            $node = $dom->createTextNode($newValue);
            $rel->appendChild($node);
            $anchor->appendChild($rel);
        }

        return $dom->saveHTML($dom->documentElement);
    }
}

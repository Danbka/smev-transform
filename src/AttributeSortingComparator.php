<?php

namespace Danbka\Smev;

class AttributeSortingComparator
{
    /**
     * Сортировка атрибутов
     * Атрибуты должны быть отсортированы в алфавитном порядке:
     * сначала по namespace URI (если атрибут - в qualified form),
     * затем – по local name.
     * Атрибуты в unqualified form после сортировки идут после атрибутов в qualified form.
     *      *
     * @param Attribute $attr1
     * @param Attribute $attr2
     * @return int
     */
    public static function compare(Attribute $attr1, Attribute $attr2): int
    {
        // оба атрибута - unqualified
        if (empty($attr1->getUri()) && empty($attr2->getUri())) {
            // сравнить имена атрибутов
            return strcmp($attr1->getName(), $attr2->getName());
        }
        
        // оба атрбута qualified
        if (!empty($attr1->getUri()) && !empty($attr2->getUri())) {
            // сравнить namespace
            $nsComparisonResult = strcmp($attr1->getUri(), $attr2->getUri());
            if ($nsComparisonResult != 0) {
                return $nsComparisonResult;
            } else {
                // если namespace атрибутов одинаковые, то сравнить имена атрибутов
                return strcmp($attr1->getName(), $attr2->getName());
            }
        }
        
        // один атрибут - qualified, другой - unqualified
        if (empty($attr1->getUri())) {
            return 1;
        } else {
            return -1;
        }
    }
}

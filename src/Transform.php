<?php

namespace Danbka\Smev;

use Danbka\Smev\Exception\TransformationException;
use Exception;
use SplDoublyLinkedList;
use XMLReader;
use XMLWriter;
use SplStack;

class Transform
{
    private const ALGORITHM_URN = "urn://smev-gov-ru/xmldsig/transform";
    
    private const XML_ENCODING = "UTF-8";
    
    private $prefixStack = null;
    
    /**
     * @param string $xml
     * @return string
     * @throws TransformationException
     */
    public function process(string $xml)
    {
        $this->prefixStack = new SplStack();
        $prefixCnt = 1;
        
        $xmlReader = null;
        $xmlWriter = null;
        
        try {
            $xmlReader = new XMLReader();
            if (!$xmlReader->XML($xml, self::XML_ENCODING)) {
                throw new Exception("Cannot load xml");
            }
            
            $xmlWriter = new XMLWriter();
            $xmlWriter->openMemory();
            
            while ($xmlReader->read()) {
                if ($xmlReader->nodeType == XMLReader::TEXT) {
                    if (!empty(trim($xmlReader->value))) {
                        $xmlWriter->text($xmlReader->value);
                    }
                    continue;
                } elseif ($xmlReader->nodeType == XMLReader::ELEMENT) {
                    $currentPrefixStack = new SplDoublyLinkedList();
                    
                    // Передача объектов всегда происходит по ссылке
                    $this->prefixStack->push($currentPrefixStack);
                    
                    $srcElement = $xmlReader->expand();
                    
                    $nsURI = $srcElement->namespaceURI ?? "";
                    $prefix = $this->findPrefix($nsURI);
                    
                    if (empty($prefix)) {
                        $prefix = "ns" . (string) $prefixCnt++;
                        $currentPrefixStack->push(new XmlNamespace($nsURI, $prefix));
                    }
                    
                    // формируем namespace самостоятельно,
                    // т.к. у XMLWriter отсутствует метод WriteAttributeString
                    $xmlWriter->startElement($prefix . ':' . $srcElement->localName);
                    
                    $srcAttributes = [];
                    for ($i = 0; $i < $srcElement->attributes->length; $i++) {
                        $srcAttr = $srcElement->attributes->item($i);
                        
                        $dstAttr = new Attribute(
                            $srcAttr->localName,
                            $srcAttr->nodeValue,
                            $srcAttr->namespaceURI,
                            $srcAttr->prefix
                        );
                        array_push($srcAttributes, $dstAttr);
                    }
                    
                    usort($srcAttributes, ["Danbka\\Smev\\AttributeSortingComparator", "compare"]);
                    
                    $dstAttributes = [];
                    /** @var Attribute $srcAttribute */
                    foreach ($srcAttributes as $srcAttribute) {
                        if (!empty($srcAttribute->getUri())) {
                            $attrPrefix = $this->findPrefix($srcAttribute->getUri());
                            if (empty($attrPrefix)) {
                                $attrPrefix = "ns" . (string) $prefixCnt++;
                                $currentPrefixStack->push(new XmlNamespace($srcAttribute->getUri(), $attrPrefix));
                            }
                            array_push($dstAttributes, new Attribute(
                                $srcAttribute->getName(),
                                $srcAttribute->getValue(),
                                $srcAttribute->getUri(),
                                $attrPrefix
                            ));
                        } else {
                            array_push($dstAttributes, new Attribute(
                                $srcAttribute->getName(),
                                $srcAttribute->getValue()
                            ));
                        }
                    }
                    
                    /**
                     * Высести namespace для текущего элемента.
                     * Дополнительная сортировка тут не нужна,
                     * т.к. атрибуты со своими namespace были отсортированы ранее
                     */
                    /** @var XmlNamespace $namespace */
                    foreach ($currentPrefixStack as $namespace) {
                        $xmlWriter->writeAttribute("xmlns:" . $namespace->getPrefix(), $namespace->getUri());
                    }
                    
                    /** @var Attribute $dstAttribute */
                    foreach ($dstAttributes as $dstAttribute) {
                        if (!empty($dstAttribute->getUri())) {
                            $xmlWriter->writeAttribute(
                                $dstAttribute->getPrefix() . ':' . $dstAttribute->getName(),
                                $dstAttribute->getValue()
                            );
                        } else {
                            $xmlWriter->writeAttribute($dstAttribute->getName(), $dstAttribute->getValue());
                        }
                    }
                    
                    // Если элемент пустой, то его необходимо закрыть сразу
                    if ($xmlReader->isEmptyElement) {
                        $xmlWriter->fullEndElement();
                    }
                    
                    continue;
                } elseif ($xmlReader->nodeType == XMLReader::END_ELEMENT) {
                    $xmlWriter->text("");
                    
                    $xmlWriter->fullEndElement();
                    
                    $this->prefixStack->pop();
                    continue;
                }
            }
            
            return $xmlWriter->outputMemory();
        } catch (Exception $exception) {
            throw new TransformationException(
                "Can not perform transformation " . self::ALGORITHM_URN,
                0,
                $exception
            );
        } finally {
            if ($xmlReader != null) {
                $xmlReader->close();
            }
            
            if ($xmlWriter != null) {
                $xmlWriter->flush();
            }
        }
    }
    
    private function findPrefix($uri)
    {
        foreach ($this->prefixStack as $currentprefixStack) {
            /** @var XmlNamespace $namespace */
            foreach ($currentprefixStack as $namespace) {
                if ($uri == $namespace->getUri()) {
                    return $namespace->getPrefix();
                }
            }
        }
        
        return null;
    }
}

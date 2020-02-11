<?php

namespace Danbka\Smev;

use InvalidArgumentException;

/**
 * Класс, описывающий namespace xml
 *
 * Class XmlNamespace
 * @package Danbka\Smev
 */
class XmlNamespace
{
    private string $prefix;
    
    private ?string $uri;
    
    public function __construct(string $uri, ?string $prefix = null)
    {
        if (empty($uri)) {
            throw new InvalidArgumentException("Passed empty URI");
        }
        
        $this->prefix = $prefix;
        
        $this->uri = $uri;
    }
    
    public function getPrefix(): string
    {
        return $this->prefix;
    }
    
    public function getUri(): string
    {
        return $this->uri;
    }
}

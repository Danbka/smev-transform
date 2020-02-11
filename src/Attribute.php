<?php

namespace Danbka\Smev;

/**
 * Класс, описывающий атрибут xml
 *
 * Class Attribute
 * @package Danbka\Smev
 */
class Attribute
{
    private string $name;
    
    private string $value;
    
    private ?string $uri;
    
    private ?string $prefix;
    
    public function __construct(string $name, string $value, ?string $uri = null, ?string $prefix = null)
    {
        
        $this->name = $name;
        $this->value = $value;
        $this->uri = $uri;
        $this->prefix = $prefix;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function getUri(): string
    {
        return (string) $this->uri;
    }
    
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}

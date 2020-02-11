<?php

namespace Danbka\Smev\Tests;

use Danbka\Smev\Attribute;
use Danbka\Smev\AttributeSortingComparator;
use PHPUnit\Framework\TestCase;

class AttributeSortingComparatorTest extends TestCase
{
    public function testWithoutNsAttr1LessThanAttr2()
    {
        $attr1 = new Attribute('name1', 'value1');
        $attr2 = new Attribute('name2', 'value2');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertLessThan(0, $result);
    }
    
    public function testWithoutNsAttr1EqualsAttr2()
    {
        $attr1 = new Attribute('name1', 'value1');
        $attr2 = new Attribute('name1', 'value2');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertEquals(0, $result);
    }
    
    public function testWithoutNsAttr1GreaterThanAttr2()
    {
        $attr1 = new Attribute('name2', 'value1');
        $attr2 = new Attribute('name1', 'value2');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertGreaterThan(0, $result);
    }
    
    public function testWithNsNs1LessThanNs2()
    {
        $attr1 = new Attribute('name1', 'value1', 'ns1', 'prefix1');
        $attr2 = new Attribute('name2', 'value2', 'ns2', 'prefix2');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertLessThan(0, $result);
    }
    
    public function testWithNsNs1GreaterThanNs2()
    {
        $attr1 = new Attribute('name2', 'value2', 'ns2', 'prefix2');
        $attr2 = new Attribute('name1', 'value1', 'ns1', 'prefix1');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertGreaterThan(0, $result);
    }
    
    public function testWithNs1WithoutNs2()
    {
        $attr1 = new Attribute('name1', 'value1', 'ns1', 'prefix1');
        $attr2 = new Attribute('name2', 'value2');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertLessThan(0, $result);
    }
    
    public function testWithoutNs1WithNs2()
    {
        $attr1 = new Attribute('name1', 'value1');
        $attr2 = new Attribute('name2', 'value2', 'ns2', 'prefix2');
        
        $result = AttributeSortingComparator::compare($attr1, $attr2);
        
        $this->assertGreaterThan(0, $result);
    }
}

<?php

namespace Danbka\Smev\Tests;

use Danbka\Smev\Transform;
use PHPUnit\Framework\TestCase;

class TransformTest extends TestCase
{
    public function testTransform()
    {
        $srcXml = '<?xml version="1.0" encoding="UTF-8"?>
            <?xml-stylesheet type="text/xsl" href="style.xsl"?>
            <elementOne xmlns="http://test/1" xmlns:qwe="http://test/2" xmlns:asd="http://test/3">
                <qwe:elementTwo>
                    <asd:elementThree
                        xmlns:wer="http://test/a"
                        xmlns:zxc="http://test/0"
                        wer:attZ="zzz"
                        attB="bbb"
                        attA="aaa"
                        zxc:attC="ccc"
                        asd:attD="ddd"
                        asd:attE="eee"
                        qwe:attF="fff"
                    />
                </qwe:elementTwo>
            </elementOne>';
        
        $expectedXml = '<ns1:elementOne xmlns:ns1="http://test/1"><ns2:elementTwo xmlns:ns2="http://test/2"><ns3:elementThree xmlns:ns3="http://test/3" xmlns:ns4="http://test/0" xmlns:ns5="http://test/a" ns4:attC="ccc" ns2:attF="fff" ns3:attD="ddd" ns3:attE="eee" ns5:attZ="zzz" attA="aaa" attB="bbb"></ns3:elementThree></ns2:elementTwo></ns1:elementOne>';
        
        $transform = new Transform();
        
        $this->assertXmlStringEqualsXmlString($expectedXml, $transform->process($srcXml));
    }
}

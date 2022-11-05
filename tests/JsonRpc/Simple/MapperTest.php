<?php

namespace Datto\JsonRpc\Simple;

use Datto\JsonRpc\Exceptions;
use Datto\Test\DeviceIdentifier;

class MapperTest extends \PHPUnit\Framework\TestCase
{
    public function testValidMapperV()
    {
        $mapper = new Mapper();
        $callable = $mapper->getCallable('math/add');

        $this->assertInstanceOf('Datto\API\Math', $callable[0]);
        $this->assertSame('add', $callable[1]);
    }

    public function testValidMapperWithAlternativeNamespaceAndSeparator()
    {
        $mapper = new Mapper('Datto\\Test', '.');
        $callable = $mapper->getCallable('math.add');

        $this->assertInstanceOf('Datto\Test\Math', $callable[0]);
        $this->assertSame('add', $callable[1]);
    }

    public function testValidMapperWithAlternativeNamespaceHierarchy()
    {
        $mapper = new Mapper('Datto\\Test', '.');
        $callable = $mapper->getCallable('Share.Nas.add');

        $this->assertInstanceOf('Datto\Test\Share\Nas', $callable[0]);
        $this->assertSame('add', $callable[1]);
    }

    public function testMapperWithTypeHintedParam()
    {
        $mapper = new Mapper('Datto\\Test');

        $method = $mapper->getCallable('offsite/getTargetType');
        $arguments = $mapper->getArguments($method, array(
            'identifier' => 'mac{aabbccdddee}'
        ));

        /** @var DeviceIdentifier $identifier */
        $identifier = $arguments[0];

        $this->assertSame('Datto\Test\DeviceIdentifier', get_class($identifier));
        $this->assertSame('dummy', $identifier->getDeviceID());
        $this->assertSame('aabbccdddee', $identifier->getMacAddress());
    }

    public function testMapperWithInvalidClass()
    {
        $this->expectException(Exceptions\MethodException::class);
        $mapper = new Mapper();
        $mapper->getCallable('INVALID/add');
    }

    public function testMapperWithIllegalSeparator()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Mapper('Datto\\API', 'a');
    }

    public function testMapperWithInvalidClassTypeHintedParam()
    {
        $this->expectException(Exceptions\ArgumentException::class);

        $mapper = new Mapper('Datto\\Test');

        $method = $mapper->getCallable('offsite/invalidEndpoint');
        $mapper->getArguments($method, array(
            'invalid' => 'dummy'
        ));
    }
}

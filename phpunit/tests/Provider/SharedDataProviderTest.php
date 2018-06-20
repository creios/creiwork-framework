<?php

namespace Creios\Creiwork\Framework\Provider;

use PHPUnit\Framework\TestCase;

class SharedDataProviderTest extends TestCase
{

    public function test()
    {
        $sharedDataProvider = new SharedDataProvider();
        $sharedDataProvider->setData(['foo' => 'bar']);
        $sharedDataProvider->addData('bar', 'foo');
        $this->assertTrue($sharedDataProvider->hasData());
        $this->assertEquals(['foo' => 'bar', 'bar' => 'foo'], $sharedDataProvider->getData());
    }
}

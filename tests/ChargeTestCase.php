<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests;

use Psr\Log\NullLogger;
use Struzik\EPPClient\Extension\IdDigital\Charge\ChargeExtension;
use Struzik\EPPClient\Extension\RGP\RGPExtension;
use Struzik\EPPClient\Tests\EPPTestCase;

class ChargeTestCase extends EPPTestCase
{
    public ChargeExtension $chargeExtension;

    protected function setUp(): void
    {
        parent::setUp();
        $this->chargeExtension = new ChargeExtension('http://www.unitedtld.com/epp/charge-1.0', new NullLogger());
        $this->eppClient->pushExtension($this->chargeExtension);
        $this->eppClient->pushExtension(new RGPExtension('urn:ietf:params:xml:ns:rgp-1.0', new NullLogger()));
    }
}

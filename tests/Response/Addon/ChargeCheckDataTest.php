<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeCheckData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Response\Domain\CheckDomainResponse;

class ChargeCheckDataTest extends ChargeTestCase
{
    public function testChargeDataWhenCheckingDomain(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <resData>
      <domain:chkData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
        <domain:cd>
          <domain:name avail="1">example.tld</domain:name>
        </domain:cd>
      </domain:chkData>
    </resData>
    <extension>
      <charge:chkData xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:cd>
          <charge:name>example.tld</charge:name>
          <charge:set>
            <charge:category name="TestBBB+">premium</charge:category>
            <charge:type>price</charge:type>
            <charge:amount command="create">15.00</charge:amount>
            <charge:amount command="renew">25.00</charge:amount>
            <charge:amount command="transfer">35.00</charge:amount>
            <charge:amount command="update" name="restore">45.00</charge:amount>
          </charge:set>
        </charge:cd>
      </charge:chkData>
    </extension>
    <trID>
      <clTRID>EppCon.190621.094753</clTRID>
      <svTRID>f922a2a9-2f26-49d8-87a6-a92ea9263411:2</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new CheckDomainResponse($xml);
        $this->chargeExtension->handleResponse($response);

        /** @var ChargeCheckData $checkDomainAddon */
        $checkDomainAddon = $response->findExtAddon(ChargeCheckData::class);
        $this->assertTrue($checkDomainAddon->isExistsChargeData('example.tld'));
        $this->assertFalse($checkDomainAddon->isExistsChargeData('example.newtld'));
        $this->assertSame('TestBBB+', $checkDomainAddon->getCategoryName('example.tld'));
        $this->assertSame('premium', $checkDomainAddon->getCategory('example.tld'));
        $this->assertSame('price', $checkDomainAddon->getType('example.tld'));
        $this->assertSame('15.00', $checkDomainAddon->getCreateAmount('example.tld'));
        $this->assertSame('25.00', $checkDomainAddon->getRenewAmount('example.tld'));
        $this->assertSame('35.00', $checkDomainAddon->getTransferAmount('example.tld'));
        $this->assertSame('45.00', $checkDomainAddon->getRestoreAmount('example.tld'));
    }
}

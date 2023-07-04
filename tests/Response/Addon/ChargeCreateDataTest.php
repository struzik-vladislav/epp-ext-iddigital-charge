<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeCreateData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Response\Domain\CreateDomainResponse;

class ChargeCreateDataTest extends ChargeTestCase
{
    public function testChargeDataWhenCreatingDomain(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <resData>
      <domain:creData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
        <domain:name>testingprem.tld</domain:name>
        <domain:crDate>2022-01-01T17:24:34.287Z</domain:crDate>
        <domain:exDate>2023-01-01T17:24:34.287Z</domain:exDate>
      </domain:creData>
    </resData>
    <extension>
      <charge:creData xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="create">15.00</charge:amount>
          <charge:amount command="renew">25.00</charge:amount>
          <charge:amount command="transfer">35.00</charge:amount>
          <charge:amount command="update" name="restore">45.00</charge:amount>
        </charge:set>
      </charge:creData>
    </extension>
    <trID>
      <clTRID>EppCon.190621.094753</clTRID>
      <svTRID>c9991fd7-c448-4d0c-9e39-030a857e074e:3</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new CreateDomainResponse($xml);
        $this->chargeExtension->handleResponse($response);

        /** @var ChargeCreateData $createDomainAddon */
        $createDomainAddon = $response->findExtAddon(ChargeCreateData::class);
        $this->assertSame('TestBBB+', $createDomainAddon->getCategoryName());
        $this->assertSame('premium', $createDomainAddon->getCategory());
        $this->assertSame('price', $createDomainAddon->getType());
        $this->assertSame('15.00', $createDomainAddon->getCreateAmount());
        $this->assertSame('25.00', $createDomainAddon->getRenewAmount());
        $this->assertSame('35.00', $createDomainAddon->getTransferAmount());
        $this->assertSame('45.00', $createDomainAddon->getRestoreAmount());
    }
}

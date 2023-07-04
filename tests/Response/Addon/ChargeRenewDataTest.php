<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeRenewData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Response\Domain\RenewDomainResponse;

class ChargeRenewDataTest extends ChargeTestCase
{
    public function testChargeDataWhenRenewingDomain(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <resData>
      <domain:renData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
        <domain:name>abc.tld</domain:name>
        <domain:exDate>2024-01-01T17:09:34.18Z</domain:exDate>
      </domain:renData>
    </resData>
    <extension>
      <charge:renData xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="create">15.00</charge:amount>
          <charge:amount command="renew">25.00</charge:amount>
          <charge:amount command="transfer">35.00</charge:amount>
          <charge:amount command="update" name="restore">45.00</charge:amount>
        </charge:set>
      </charge:renData>
    </extension>
    <trID>
      <clTRID>ABC-12345</clTRID>
      <svTRID>d370d5c9-a67d-4499-861c-729e230f274f:191</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new RenewDomainResponse($xml);
        $this->chargeExtension->handleResponse($response);

        /** @var ChargeRenewData $renewDomainAddon */
        $renewDomainAddon = $response->findExtAddon(ChargeRenewData::class);
        $this->assertSame('TestBBB+', $renewDomainAddon->getCategoryName());
        $this->assertSame('premium', $renewDomainAddon->getCategory());
        $this->assertSame('price', $renewDomainAddon->getType());
        $this->assertSame('15.00', $renewDomainAddon->getCreateAmount());
        $this->assertSame('25.00', $renewDomainAddon->getRenewAmount());
        $this->assertSame('35.00', $renewDomainAddon->getTransferAmount());
        $this->assertSame('45.00', $renewDomainAddon->getRestoreAmount());
    }
}

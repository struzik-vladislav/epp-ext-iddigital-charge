<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeUpdateData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Response\Domain\UpdateDomainResponse;

class ChargeUpdateDataTest extends ChargeTestCase
{
    public function testChargeDataWhenUpdatingDomain(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
  <response>
    <result code="1000">
      <msg>Command completed successfully</msg>
    </result>
    <extension>
      <rgp:upData xmlns:rgp="urn:ietf:params:xml:ns:rgp-1.0">
        <rgp:rgpStatus s="pendingRestore" />
      </rgp:upData>
      <charge:upData xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="create">15.00</charge:amount>
          <charge:amount command="renew">25.00</charge:amount>
          <charge:amount command="transfer">35.00</charge:amount>
          <charge:amount command="update" name="restore">45.00</charge:amount>
        </charge:set>
      </charge:upData>
    </extension>
    <trID>
      <clTRID>ABC-12345</clTRID>
      <svTRID>4dc75168-b147-4ac8-a961-57c21868e5eb:25</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new UpdateDomainResponse($xml);
        $this->chargeExtension->handleResponse($response);

        /** @var ChargeUpdateData $updateDomainAddon */
        $updateDomainAddon = $response->findExtAddon(ChargeUpdateData::class);
        $this->assertSame('TestBBB+', $updateDomainAddon->getCategoryName());
        $this->assertSame('premium', $updateDomainAddon->getCategory());
        $this->assertSame('price', $updateDomainAddon->getType());
        $this->assertSame('15.00', $updateDomainAddon->getCreateAmount());
        $this->assertSame('25.00', $updateDomainAddon->getRenewAmount());
        $this->assertSame('35.00', $updateDomainAddon->getTransferAmount());
        $this->assertSame('45.00', $updateDomainAddon->getRestoreAmount());
    }
}

<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeTransferData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Response\Domain\TransferDomainResponse;

class ChargeTransferDataTest extends ChargeTestCase
{
    public function testChargeDataWhenTransferringDomain(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
  <response>
    <result code="1001">
      <msg>Command completed successfully; action pending</msg>
    </result>
    <resData>
      <domain:trnData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
        <domain:name>testingprem.tld</domain:name>
        <domain:trStatus>pending</domain:trStatus>
        <domain:reID>Registrar9400</domain:reID>
        <domain:reDate>2022-01-01T17:57:50.43Z</domain:reDate>
        <domain:acID>Registrar9300</domain:acID>
        <domain:acDate>2022-01-01T17:57:50.43Z</domain:acDate>
        <domain:exDate>2023-02-01T17:24:34.287Z</domain:exDate>
      </domain:trnData>
    </resData>
    <extension>
      <charge:trnData xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="create">15.00</charge:amount>
          <charge:amount command="renew">25.00</charge:amount>
          <charge:amount command="transfer">35.00</charge:amount>
          <charge:amount command="update" name="restore">45.00</charge:amount>
        </charge:set>
      </charge:trnData>
    </extension>
    <trID>
      <clTRID>ABC-12345</clTRID>
      <svTRID>7c0e274c-c32c-49d4-952c-3552c359e60e:19</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new TransferDomainResponse($xml);
        $this->chargeExtension->handleResponse($response);

        /** @var ChargeTransferData $transferDomainAddon */
        $transferDomainAddon = $response->findExtAddon(ChargeTransferData::class);
        $this->assertSame('TestBBB+', $transferDomainAddon->getCategoryName());
        $this->assertSame('premium', $transferDomainAddon->getCategory());
        $this->assertSame('price', $transferDomainAddon->getType());
        $this->assertSame('15.00', $transferDomainAddon->getCreateAmount());
        $this->assertSame('25.00', $transferDomainAddon->getRenewAmount());
        $this->assertSame('35.00', $transferDomainAddon->getTransferAmount());
        $this->assertSame('45.00', $transferDomainAddon->getRestoreAmount());
    }
}

<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeInfoData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Response\Domain\InfoDomainResponse;

class ChargeInfoDataTest extends ChargeTestCase
{
    public function testChargeDataWhenGettingDomainInfo(): void
    {
        $xml = <<<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:ietf:params:xml:ns:epp-1.0 epp-1.0.xsd">
  <response>
    <result code="1000">
      <msg>Domain Info Command completed successfully</msg>
    </result>
    <resData>
      <domain:infData xmlns:domain="urn:ietf:params:xml:ns:domain-1.0" xsi:schemaLocation="urn:ietf:params:xml:ns:domain-1.0 domain-1.0.xsd">
        <domain:name>example.tld</domain:name>
        <domain:roid>DOM_1E-PDT</domain:roid>
        <domain:status s="ok" />
        <domain:registrant>registrantcontact</domain:registrant>
        <domain:contact type="admin">admincontact</domain:contact>
        <domain:contact type="tech">techcontact</domain:contact>
        <domain:contact type="billing">billingcontact</domain:contact>
        <domain:contact type="reseller">resellercontact</domain:contact>
        <domain:ns>
          <domain:hostAttr>
            <domain:hostName>ns1.example.tld</domain:hostName>
            <domain:hostAddr ip="v4">y.y.y.y</domain:hostAddr>
            <domain:hostAddr ip="v6">ff02::1</domain:hostAddr>
          </domain:hostAttr>
          <domain:hostAttr>
            <domain:hostName>ns1.fuzzy.tld</domain:hostName>
          </domain:hostAttr>
        </domain:ns>
        <domain:clID>currentregistrar</domain:clID>
        <domain:crID>createregistrar</domain:crID>
        <domain:crDate>2010-01-01T04:48:50Z</domain:crDate>
        <domain:upID>currentregistrar</domain:upID>
        <domain:upDate>2010-02-02T04:48:50Z</domain:upDate>
        <domain:exDate>2011-01-01T04:48:50Z</domain:exDate>
      </domain:infData>
    </resData>
    <extension>
      <charge:infData xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="create">15.00</charge:amount>
          <charge:amount command="renew">25.00</charge:amount>
          <charge:amount command="transfer">35.00</charge:amount>
          <charge:amount command="update" name="restore">45.00</charge:amount>
        </charge:set>
      </charge:infData>
    </extension>
    <trID>
      <clTRID>EppCon.190621.094753</clTRID>
      <svTRID>f922a2a9-2f26-49d8-87a6-a92ea9263411:2</svTRID>
    </trID>
  </response>
</epp>
EOF;
        $response = new InfoDomainResponse($xml);
        $this->chargeExtension->handleResponse($response);

        /** @var ChargeInfoData $infoDomainAddon */
        $infoDomainAddon = $response->findExtAddon(ChargeInfoData::class);
        $this->assertSame('TestBBB+', $infoDomainAddon->getCategoryName());
        $this->assertSame('premium', $infoDomainAddon->getCategory());
        $this->assertSame('price', $infoDomainAddon->getType());
        $this->assertSame('15.00', $infoDomainAddon->getCreateAmount());
        $this->assertSame('25.00', $infoDomainAddon->getRenewAmount());
        $this->assertSame('35.00', $infoDomainAddon->getTransferAmount());
        $this->assertSame('45.00', $infoDomainAddon->getRestoreAmount());
    }
}

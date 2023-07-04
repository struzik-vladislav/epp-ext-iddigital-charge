<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Tests\Request\Addon;

use DateTime;
use Struzik\EPPClient\Extension\IdDigital\Charge\Request\Addon\ChargeAgreement;
use Struzik\EPPClient\Extension\IdDigital\Charge\Tests\ChargeTestCase;
use Struzik\EPPClient\Extension\RGP\Request\RGPRequestRestoreRequest;
use Struzik\EPPClient\Node\Domain\DomainContactNode;
use Struzik\EPPClient\Node\Domain\DomainPeriodNode;
use Struzik\EPPClient\Request\Domain\CreateDomainRequest;
use Struzik\EPPClient\Request\Domain\RenewDomainRequest;
use Struzik\EPPClient\Request\Domain\RequestDomainTransferRequest;

class ChargeAgreementTest extends ChargeTestCase
{
    public function testChargeAgreementWhenCreatingDomain()
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <create>
      <domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>testingprem.tld</domain:name>
        <domain:period unit="y">1</domain:period>
        <domain:registrant>abc123</domain:registrant>
        <domain:contact type="admin">abc123</domain:contact>
        <domain:contact type="tech">abc123</domain:contact>
        <domain:authInfo>
          <domain:pw>2fooBAR</domain:pw>
        </domain:authInfo>
      </domain:create>
    </create>
    <extension>
      <charge:agreement xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="create">25.00</charge:amount>
        </charge:set>
      </charge:agreement>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new CreateDomainRequest($this->eppClient);
        $request->setDomain('testingprem.tld');
        $request->setPeriod(1);
        $request->setUnit(DomainPeriodNode::UNIT_YEAR);
        $request->setRegistrant('abc123');
        $request->setContacts([
            DomainContactNode::TYPE_ADMIN => 'abc123',
            DomainContactNode::TYPE_TECH => 'abc123',
        ]);
        $request->setPassword('2fooBAR');
        $chargeAddon = (new ChargeAgreement())
            ->setCategory('premium')
            ->setCategoryName('TestBBB+')
            ->setType('price')
            ->setCreateAmount('25.00');
        $request->addExtAddon($chargeAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('premium', $chargeAddon->getCategory());
        $this->assertSame('TestBBB+', $chargeAddon->getCategoryName());
        $this->assertSame('price', $chargeAddon->getType());
        $this->assertSame('25.00', $chargeAddon->getCreateAmount());
        $this->assertSame(null, $chargeAddon->getRenewAmount());
        $this->assertSame(null, $chargeAddon->getTransferAmount());
        $this->assertSame(null, $chargeAddon->getRestoreAmount());
    }

    public function testChargeAgreementWhenRenewingDomain()
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <renew>
      <domain:renew xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>abc.tld</domain:name>
        <domain:curExpDate>2023-01-01</domain:curExpDate>
        <domain:period unit="y">1</domain:period>
      </domain:renew>
    </renew>
    <extension>
      <charge:agreement xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="BBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="renew">25.00</charge:amount>
        </charge:set>
      </charge:agreement>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new RenewDomainRequest($this->eppClient);
        $request->setDomain('abc.tld');
        $request->setExpiryDate(DateTime::createFromFormat('!Y-m-d', '2023-01-01'));
        $request->setPeriod(1);
        $request->setUnit(DomainPeriodNode::UNIT_YEAR);
        $chargeAddon = (new ChargeAgreement())
            ->setCategory('premium')
            ->setCategoryName('BBB+')
            ->setType('price')
            ->setRenewAmount('25.00');
        $request->addExtAddon($chargeAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('premium', $chargeAddon->getCategory());
        $this->assertSame('BBB+', $chargeAddon->getCategoryName());
        $this->assertSame('price', $chargeAddon->getType());
        $this->assertSame(null, $chargeAddon->getCreateAmount());
        $this->assertSame('25.00', $chargeAddon->getRenewAmount());
        $this->assertSame(null, $chargeAddon->getTransferAmount());
        $this->assertSame(null, $chargeAddon->getRestoreAmount());
    }

    public function testChargeAgreementWhenTransferringDomain()
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <transfer op="request">
      <domain:transfer xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>testingprem.tld</domain:name>
        <domain:authInfo>
          <domain:pw>Password_1</domain:pw>
        </domain:authInfo>
      </domain:transfer>
    </transfer>
    <extension>
      <charge:agreement xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="transfer">25.00</charge:amount>
        </charge:set>
      </charge:agreement>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new RequestDomainTransferRequest($this->eppClient);
        $request->setDomain('testingprem.tld');
        $request->setPassword('Password_1');
        $chargeAddon = (new ChargeAgreement())
            ->setCategory('premium')
            ->setCategoryName('TestBBB+')
            ->setType('price')
            ->setTransferAmount('25.00');
        $request->addExtAddon($chargeAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('premium', $chargeAddon->getCategory());
        $this->assertSame('TestBBB+', $chargeAddon->getCategoryName());
        $this->assertSame('price', $chargeAddon->getType());
        $this->assertSame(null, $chargeAddon->getCreateAmount());
        $this->assertSame(null, $chargeAddon->getRenewAmount());
        $this->assertSame('25.00', $chargeAddon->getTransferAmount());
        $this->assertSame(null, $chargeAddon->getRestoreAmount());
    }

    public function testChargeAgreementWhenRestoringDomain()
    {
        $expected = <<<'EOF'
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
  <command>
    <update>
      <domain:update xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
        <domain:name>testprem.tld</domain:name>
        <domain:chg/>
      </domain:update>
    </update>
    <extension>
      <rgp:update xmlns:rgp="urn:ietf:params:xml:ns:rgp-1.0">
        <rgp:restore op="request"/>
      </rgp:update>
      <charge:agreement xmlns:charge="http://www.unitedtld.com/epp/charge-1.0">
        <charge:set>
          <charge:category name="TestBBB+">premium</charge:category>
          <charge:type>price</charge:type>
          <charge:amount command="update" name="restore">25.00</charge:amount>
        </charge:set>
      </charge:agreement>
    </extension>
    <clTRID>TEST-REQUEST-ID</clTRID>
  </command>
</epp>

EOF;
        $request = new RGPRequestRestoreRequest($this->eppClient);
        $request->setDomain('testprem.tld');
        $chargeAddon = (new ChargeAgreement())
            ->setCategory('premium')
            ->setCategoryName('TestBBB+')
            ->setType('price')
            ->setRestoreAmount('25.00');
        $request->addExtAddon($chargeAddon);
        $request->build();

        $this->assertSame($expected, $request->getDocument()->saveXML());
        $this->assertSame('premium', $chargeAddon->getCategory());
        $this->assertSame('TestBBB+', $chargeAddon->getCategoryName());
        $this->assertSame('price', $chargeAddon->getType());
        $this->assertSame(null, $chargeAddon->getCreateAmount());
        $this->assertSame(null, $chargeAddon->getRenewAmount());
        $this->assertSame(null, $chargeAddon->getTransferAmount());
        $this->assertSame('25.00', $chargeAddon->getRestoreAmount());
    }
}

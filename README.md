# Identity Digital Charge Extension for EPP Client

![Build Status](https://github.com/struzik-vladislav/epp-ext-iddigital-charge/actions/workflows/ci.yml/badge.svg?branch=master)
[![Latest Stable Version](https://img.shields.io/github/v/release/struzik-vladislav/epp-ext-iddigital-charge?sort=semver&style=flat-square)](https://packagist.org/packages/struzik-vladislav/epp-ext-iddigital-charge)
[![Total Downloads](https://img.shields.io/packagist/dt/struzik-vladislav/epp-ext-iddigital-charge?style=flat-square)](https://packagist.org/packages/struzik-vladislav/epp-ext-iddigital-charge/stats)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![StandWithUkraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/badges/StandWithUkraine.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

Charge extension provided by [Identity Digital](https://www.identity.digital/).

Extension for [struzik-vladislav/epp-client](https://github.com/struzik-vladislav/epp-client) library.

## Usage

```php
<?php

use Psr\Log\NullLogger;
use Struzik\EPPClient\EPPClient;
use Struzik\EPPClient\Extension\IdDigital\Charge\ChargeExtension;
use Struzik\EPPClient\Extension\IdDigital\Charge\Request\Addon\ChargeAgreement;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeCheckData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeCreateData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeRenewData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeTransferData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeUpdateData;
use Struzik\EPPClient\Extension\RGP\Request\RGPRequestRestoreRequest;
use Struzik\EPPClient\Node\Domain\DomainContactNode;
use Struzik\EPPClient\Node\Domain\DomainPeriodNode;
use Struzik\EPPClient\Request\Domain\CheckDomainRequest;
use Struzik\EPPClient\Request\Domain\CreateDomainRequest;
use Struzik\EPPClient\Request\Domain\RenewDomainRequest;
use Struzik\EPPClient\Request\Domain\RequestDomainTransferRequest;

// ...

$client->pushExtension(new ChargeExtension('http://www.unitedtld.com/epp/charge-1.0', new NullLogger()));

// ...

/**
 * Domain check example.
 */
$request = new CheckDomainRequest($client);
$request->addDomain('premium.tld');

$response = $client->send($request);
$checkDomainAddon = $response->findExtAddon(ChargeCheckData::class);
if ($checkDomainAddon instanceof ChargeCheckData and $checkDomainAddon->isExistsChargeData('premium.tld')) {
    $category = $checkDomainAddon->getCategory('premium.tld');
    $categoryName = $checkDomainAddon->getCategoryName('premium.tld');
    $type = $checkDomainAddon->getType('premium.tld');
    $createAmount = $checkDomainAddon->getCreateAmount('premium.tld');
    $renewAmount = $checkDomainAddon->getRenewAmount('premium.tld');
    $transferAmount = $checkDomainAddon->getTransferAmount('premium.tld');
    $restoreAmount = $checkDomainAddon->getRestoreAmount('premium.tld');
}

/**
 * Domain create example.
 */
$request = new CreateDomainRequest($client);
$request->setDomain('premium.tld');
$request->setPeriod(1);
$request->setUnit(DomainPeriodNode::UNIT_YEAR);
$request->setRegistrant('abc123');
$request->setContacts([
    DomainContactNode::TYPE_ADMIN => 'abc123',
    DomainContactNode::TYPE_TECH => 'abc123',
]);
$request->setPassword('2fooBAR');
$agreementAddon = (new ChargeAgreement())
    ->setCategory('premium')
    ->setCategoryName('AAA')
    ->setType('price')
    ->setCreateAmount('25.00');
$request->addExtAddon($agreementAddon);

$response = $client->send($request);
$createDomainAddon = $response->findExtAddon(ChargeCreateData::class);
if ($createDomainAddon instanceof ChargeCreateData) {
    $category = $createDomainAddon->getCategory();
    $categoryName = $createDomainAddon->getCategoryName();
    $type = $createDomainAddon->getType();
    $createAmount = $createDomainAddon->getCreateAmount();
    $renewAmount = $createDomainAddon->getRenewAmount();
    $transferAmount = $createDomainAddon->getTransferAmount();
    $restoreAmount = $createDomainAddon->getRestoreAmount();
}

/**
 * Domain renew example.
 */
$request = new RenewDomainRequest($client);
$request->setDomain('premium.tld');
$request->setExpiryDate(DateTime::createFromFormat('!Y-m-d', '2023-01-01'));
$request->setPeriod(1);
$request->setUnit(DomainPeriodNode::UNIT_YEAR);
$agreementAddon = (new ChargeAgreement())
    ->setCategory('premium')
    ->setCategoryName('AAA')
    ->setType('price')
    ->setRenewAmount('25.00');
$request->addExtAddon($agreementAddon);

$response = $client->send($request);
$renewDomainAddon = $response->findExtAddon(ChargeRenewData::class);
if ($renewDomainAddon instanceof ChargeRenewData) {
    $category = $renewDomainAddon->getCategory();
    $categoryName = $renewDomainAddon->getCategoryName();
    $type = $renewDomainAddon->getType();
    $createAmount = $renewDomainAddon->getCreateAmount();
    $renewAmount = $renewDomainAddon->getRenewAmount();
    $transferAmount = $renewDomainAddon->getTransferAmount();
    $restoreAmount = $renewDomainAddon->getRestoreAmount();
}

/**
 * Domain transfer example.
 */
$request = new RequestDomainTransferRequest($client);
$request->setDomain('premium.tld');
$request->setPassword('2fooBAR');
$agreementAddon = (new ChargeAgreement())
    ->setCategory('premium')
    ->setCategoryName('AAA')
    ->setType('price')
    ->setTransferAmount('25.00');
$request->addExtAddon($agreementAddon);

$response = $client->send($request);
$transferDomainAddon = $response->findExtAddon(ChargeTransferData::class);
if ($transferDomainAddon instanceof ChargeTransferData) {
    $category = $transferDomainAddon->getCategory();
    $categoryName = $transferDomainAddon->getCategoryName();
    $type = $transferDomainAddon->getType();
    $createAmount = $transferDomainAddon->getCreateAmount();
    $renewAmount = $transferDomainAddon->getRenewAmount();
    $transferAmount = $transferDomainAddon->getTransferAmount();
    $restoreAmount = $transferDomainAddon->getRestoreAmount();
}

/**
 * Domain restore example.
 */
$request = new RGPRequestRestoreRequest($client);
$request->setDomain('testprem.tld');
$agreementAddon = (new ChargeAgreement())
    ->setCategory('premium')
    ->setCategoryName('AAA')
    ->setType('price')
    ->setRestoreAmount('25.00');
$request->addExtAddon($agreementAddon);

$response = $client->send($request);
$updateDomainAddon = $response->findExtAddon(ChargeUpdateData::class);
if ($updateDomainAddon instanceof ChargeUpdateData) {
    $category = $updateDomainAddon->getCategory();
    $categoryName = $updateDomainAddon->getCategoryName();
    $type = $updateDomainAddon->getType();
    $createAmount = $updateDomainAddon->getCreateAmount();
    $renewAmount = $updateDomainAddon->getRenewAmount();
    $transferAmount = $updateDomainAddon->getTransferAmount();
    $restoreAmount = $updateDomainAddon->getRestoreAmount();
}

```

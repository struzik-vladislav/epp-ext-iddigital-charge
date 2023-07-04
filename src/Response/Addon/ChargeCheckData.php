<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon;

use DOMNode;
use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Response\ResponseInterface;
use XPath;

class ChargeCheckData
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Check for special prices for the domain.
     *
     * @param string $domain fully qualified name of the domain
     */
    public function isExistsChargeData(string $domain): bool
    {
        try {
            $this->getChargecd($domain);
        } catch (UnexpectedValueException $e) {
            return false;
        }

        return true;
    }

    public function getCategoryName(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $node = $this->response->getFirst('charge:set/charge:category', $chargecdNode);

        return $node->getAttribute('name');
    }

    public function getCategory(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $node = $this->response->getFirst('charge:set/charge:category', $chargecdNode);

        return $node->nodeValue;
    }

    public function getType(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $node = $this->response->getFirst('charge:set/charge:type', $chargecdNode);

        return $node->nodeValue;
    }

    public function getCreateAmount(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $xpathQuery = sprintf('charge:set/charge:amount[@command=\'%s\']', 'create');
        $node = $this->response->getFirst($xpathQuery, $chargecdNode);

        return $node->nodeValue;
    }

    public function getRenewAmount(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $xpathQuery = sprintf('charge:set/charge:amount[@command=\'%s\']', 'renew');
        $node = $this->response->getFirst($xpathQuery, $chargecdNode);

        return $node->nodeValue;
    }

    public function getTransferAmount(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $xpathQuery = sprintf('charge:set/charge:amount[@command=\'%s\']', 'transfer');
        $node = $this->response->getFirst($xpathQuery, $chargecdNode);

        return $node->nodeValue;
    }

    public function getRestoreAmount(string $domain): string
    {
        $chargecdNode = $this->getChargecd($domain);
        $xpathQuery = sprintf('charge:set/charge:amount[@command=\'%s\' and @name=\'%s\']', 'update', 'restore');
        $node = $this->response->getFirst($xpathQuery, $chargecdNode);

        return $node->nodeValue;
    }

    /**
     * Getting the <charge:cd> node by domain name.
     *
     * @param string $domain fully qualified name of the domain
     *
     * @throws UnexpectedValueException
     */
    protected function getChargecd(string $domain): DOMNode
    {
        $pattern = '//epp:epp/epp:response/epp:extension/charge:chkData/charge:cd[charge:name[php:functionString("XPath\quote", text()) = \'%s\']]';
        $query = sprintf($pattern, XPath\quote($domain));
        $node = $this->response->getFirst($query);

        if (null === $node) {
            throw new UnexpectedValueException(sprintf('Domain [%s] not found.', $domain));
        }

        return $node;
    }
}

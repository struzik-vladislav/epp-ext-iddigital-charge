<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon;

use Struzik\EPPClient\Extension\IdDigital\Charge\Node\ChargeAmountNode;
use Struzik\EPPClient\Response\ResponseInterface;

class ChargeRenewData
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getCategoryName(): string
    {
        $node = $this->response->getFirst('//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:category');

        return $node->getAttribute('name');
    }

    public function getCategory(): string
    {
        $node = $this->response->getFirst('//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:category');

        return $node->nodeValue;
    }

    public function getType(): string
    {
        $node = $this->response->getFirst('//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:type');

        return $node->nodeValue;
    }

    public function getCreateAmount(): string
    {
        $xpathQuery = sprintf(
            '//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:amount[@command=\'%s\']',
            ChargeAmountNode::COMMAND_CREATE
        );
        $node = $this->response->getFirst($xpathQuery);

        return $node->nodeValue;
    }

    public function getRenewAmount(): string
    {
        $xpathQuery = sprintf(
            '//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:amount[@command=\'%s\']',
            ChargeAmountNode::COMMAND_RENEW
        );
        $node = $this->response->getFirst($xpathQuery);

        return $node->nodeValue;
    }

    public function getTransferAmount(): string
    {
        $xpathQuery = sprintf(
            '//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:amount[@command=\'%s\']',
            ChargeAmountNode::COMMAND_TRANSFER
        );
        $node = $this->response->getFirst($xpathQuery);

        return $node->nodeValue;
    }

    public function getRestoreAmount(): string
    {
        $xpathQuery = sprintf(
            '//epp:epp/epp:response/epp:extension/charge:renData/charge:set/charge:amount[@command=\'%s\' and @name=\'%s\']',
            ChargeAmountNode::COMMAND_UPDATE,
            ChargeAmountNode::NAME_RESTORE
        );
        $node = $this->response->getFirst($xpathQuery);

        return $node->nodeValue;
    }
}

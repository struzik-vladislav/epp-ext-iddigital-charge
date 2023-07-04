<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Node;

use DOMElement;
use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Extension\IdDigital\Charge\ChargeExtension;
use Struzik\EPPClient\Request\RequestInterface;

class ChargeAgreementNode
{
    public static function create(RequestInterface $request, DOMElement $parentNode): DOMElement
    {
        $namespace = $request->getClient()
            ->getExtNamespaceCollection()
            ->offsetGet(ChargeExtension::NS_NAME_CHARGE);
        if (!$namespace) {
            throw new UnexpectedValueException('URI of the "charge" namespace cannot be empty.');
        }

        $node = $request->getDocument()->createElement('charge:agreement');
        $node->setAttribute('xmlns:charge', $namespace);
        $parentNode->appendChild($node);

        return $node;
    }
}

<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Node;

use DOMElement;
use Struzik\EPPClient\Request\RequestInterface;

class ChargeSetNode
{
    public static function create(RequestInterface $request, DOMElement $parentNode): DOMElement
    {
        $node = $request->getDocument()->createElement('charge:set');
        $parentNode->appendChild($node);

        return $node;
    }
}

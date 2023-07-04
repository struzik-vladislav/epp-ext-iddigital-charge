<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Node;

use DOMElement;
use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class ChargeTypeNode
{
    public static function create(RequestInterface $request, DOMElement $parentNode, string $type): DOMElement
    {
        if ('' === $type) {
            throw new InvalidArgumentException('Invalid parameter "type".');
        }

        $node = $request->getDocument()->createElement('charge:type', $type);
        $parentNode->appendChild($node);

        return $node;
    }
}

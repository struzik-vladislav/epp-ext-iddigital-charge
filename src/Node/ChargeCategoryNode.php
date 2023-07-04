<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Node;

use DOMElement;
use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class ChargeCategoryNode
{
    public static function create(RequestInterface $request, DOMElement $parentNode, string $category, string $name): DOMElement
    {
        if ('' === $category) {
            throw new InvalidArgumentException('Invalid parameter "category".');
        }
        if ('' === $name) {
            throw new InvalidArgumentException('Invalid parameter "name".');
        }

        $node = $request->getDocument()->createElement('charge:category', $category);
        $node->setAttribute('name', $name);
        $parentNode->appendChild($node);

        return $node;
    }
}

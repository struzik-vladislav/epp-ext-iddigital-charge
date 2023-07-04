<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Node;

use DOMElement;
use Struzik\EPPClient\Exception\InvalidArgumentException;
use Struzik\EPPClient\Request\RequestInterface;

class ChargeAmountNode
{
    public const COMMAND_CREATE = 'create';
    public const COMMAND_RENEW = 'renew';
    public const COMMAND_TRANSFER = 'transfer';
    public const COMMAND_UPDATE = 'update';
    public const NAME_RESTORE = 'restore';

    public static function create(RequestInterface $request, DOMElement $parentNode, string $amount, string $command, ?string $name): DOMElement
    {
        if ('' === $amount) {
            throw new InvalidArgumentException('Invalid parameter "amount".');
        }
        if (!in_array($command, [self::COMMAND_CREATE, self::COMMAND_RENEW, self::COMMAND_TRANSFER, self::COMMAND_UPDATE], true)) {
            throw new InvalidArgumentException('Invalid parameter "command".');
        }
        if (!in_array($name, [null, self::NAME_RESTORE], true)) {
            throw new InvalidArgumentException('Invalid parameter "name".');
        }

        $node = $request->getDocument()->createElement('charge:amount', $amount);
        $node->setAttribute('command', $command);
        if (null !== $name) {
            $node->setAttribute('name', $name);
        }
        $parentNode->appendChild($node);

        return $node;
    }
}

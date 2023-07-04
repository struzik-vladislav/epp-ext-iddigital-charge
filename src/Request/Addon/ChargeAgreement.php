<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge\Request\Addon;

use Struzik\EPPClient\Exception\UnexpectedValueException;
use Struzik\EPPClient\Extension\IdDigital\Charge\Node\ChargeAgreementNode;
use Struzik\EPPClient\Extension\IdDigital\Charge\Node\ChargeAmountNode;
use Struzik\EPPClient\Extension\IdDigital\Charge\Node\ChargeCategoryNode;
use Struzik\EPPClient\Extension\IdDigital\Charge\Node\ChargeSetNode;
use Struzik\EPPClient\Extension\IdDigital\Charge\Node\ChargeTypeNode;
use Struzik\EPPClient\Extension\RequestAddonInterface;
use Struzik\EPPClient\Node\Common\ExtensionNode;
use Struzik\EPPClient\Request\RequestInterface;

class ChargeAgreement implements RequestAddonInterface
{
    private string $category = '';
    private string $categoryName = '';
    private string $type = '';
    private ?string $createAmount = null;
    private ?string $renewAmount = null;
    private ?string $transferAmount = null;
    private ?string $restoreAmount = null;

    public function build(RequestInterface $request): void
    {
        if (!($this->createAmount xor $this->renewAmount xor $this->transferAmount xor $this->restoreAmount)) {
            throw new UnexpectedValueException('Invalid parameters "createAmount" or "renewAmount" or "transferAmount" or "restoreAmount". One of them must be set.');
        }

        $extensionNode = ExtensionNode::create($request);
        $agreementNode = ChargeAgreementNode::create($request, $extensionNode);
        $setNode = ChargeSetNode::create($request, $agreementNode);
        ChargeCategoryNode::create($request, $setNode, $this->category, $this->categoryName);
        ChargeTypeNode::create($request, $setNode, $this->type);
        if (null !== $this->createAmount) {
            ChargeAmountNode::create($request, $setNode, $this->createAmount, ChargeAmountNode::COMMAND_CREATE, null);
        }
        if (null !== $this->renewAmount) {
            ChargeAmountNode::create($request, $setNode, $this->renewAmount, ChargeAmountNode::COMMAND_RENEW, null);
        }
        if (null !== $this->transferAmount) {
            ChargeAmountNode::create($request, $setNode, $this->transferAmount, ChargeAmountNode::COMMAND_TRANSFER, null);
        }
        if (null !== $this->restoreAmount) {
            ChargeAmountNode::create($request, $setNode, $this->restoreAmount, ChargeAmountNode::COMMAND_UPDATE, ChargeAmountNode::NAME_RESTORE);
        }
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getCategoryName(): string
    {
        return $this->categoryName;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setCreateAmount(?string $createAmount): self
    {
        $this->createAmount = $createAmount;

        return $this;
    }

    public function getCreateAmount(): ?string
    {
        return $this->createAmount;
    }

    public function setRenewAmount(?string $renewAmount): self
    {
        $this->renewAmount = $renewAmount;

        return $this;
    }

    public function getRenewAmount(): ?string
    {
        return $this->renewAmount;
    }

    public function setTransferAmount(?string $transferAmount): self
    {
        $this->transferAmount = $transferAmount;

        return $this;
    }

    public function getTransferAmount(): ?string
    {
        return $this->transferAmount;
    }

    public function setRestoreAmount(?string $restoreAmount): self
    {
        $this->restoreAmount = $restoreAmount;

        return $this;
    }

    public function getRestoreAmount(): ?string
    {
        return $this->restoreAmount;
    }
}

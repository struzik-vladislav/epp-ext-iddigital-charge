<?php

namespace Struzik\EPPClient\Extension\IdDigital\Charge;

use Psr\Log\LoggerInterface;
use Struzik\EPPClient\EPPClient;
use Struzik\EPPClient\Extension\ExtensionInterface;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeCheckData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeCreateData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeRenewData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeTransferData;
use Struzik\EPPClient\Extension\IdDigital\Charge\Response\Addon\ChargeUpdateData;
use Struzik\EPPClient\Response\ResponseInterface;

/**
 * Charge extension provided by Identity Digital (https://www.identity.digital/).
 */
class ChargeExtension implements ExtensionInterface
{
    public const NS_NAME_CHARGE = 'charge';

    private string $uri;
    private LoggerInterface $logger;

    /**
     * @param string          $uri    URI of the charge extension
     * @param LoggerInterface $logger instance of logger object
     */
    public function __construct(string $uri, LoggerInterface $logger)
    {
        $this->uri = $uri;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setupNamespaces(EPPClient $client): void
    {
        $client->getExtNamespaceCollection()
            ->offsetSet(self::NS_NAME_CHARGE, $this->uri);
    }

    /**
     * {@inheritdoc}
     */
    public function handleResponse(ResponseInterface $response): void
    {
        if (!in_array($this->uri, $response->getUsedNamespaces(), true)) {
            $this->logger->debug(sprintf('Namespace with URI "%s" does not exists in used namespaces in the response object.', $this->uri));

            return;
        }

        $node = $response->getFirst('//charge:chkData');
        if (null !== $node) {
            $this->logger->debug(sprintf('Adding add-on "%s" to the response object.', ChargeCheckData::class));
            $response->addExtAddon(new ChargeCheckData($response));
        }

        $node = $response->getFirst('//charge:creData');
        if (null !== $node) {
            $this->logger->debug(sprintf('Adding add-on "%s" to the response object.', ChargeCreateData::class));
            $response->addExtAddon(new ChargeCreateData($response));
        }

        $node = $response->getFirst('//charge:renData');
        if (null !== $node) {
            $this->logger->debug(sprintf('Adding add-on "%s" to the response object.', ChargeRenewData::class));
            $response->addExtAddon(new ChargeRenewData($response));
        }

        $node = $response->getFirst('//charge:trnData');
        if (null !== $node) {
            $this->logger->debug(sprintf('Adding add-on "%s" to the response object.', ChargeTransferData::class));
            $response->addExtAddon(new ChargeTransferData($response));
        }

        $node = $response->getFirst('//charge:upData');
        if (null !== $node) {
            $this->logger->debug(sprintf('Adding add-on "%s" to the response object.', ChargeUpdateData::class));
            $response->addExtAddon(new ChargeUpdateData($response));
        }
    }
}

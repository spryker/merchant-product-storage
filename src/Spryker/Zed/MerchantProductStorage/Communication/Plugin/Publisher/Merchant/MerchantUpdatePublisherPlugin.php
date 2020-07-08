<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantProductStorage\Communication\Plugin\Publisher\Merchant;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Merchant\Dependency\MerchantEvents;
use Spryker\Zed\PublisherExtension\Dependency\Plugin\PublisherPluginInterface;

/**
 * @method \Spryker\Zed\MerchantProductStorage\MerchantProductStorageConfig getConfig()
 * @method \Spryker\Zed\MerchantProductStorage\Business\MerchantProductStorageFacadeInterface getFacade()
 * @method \Spryker\Zed\MerchantProductStorage\Communication\MerchantProductStorageCommunicationFactory getFactory()
 */
class MerchantUpdatePublisherPlugin extends AbstractPlugin implements PublisherPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\EventEntityTransfer[] $transfers
     * @param string $eventName
     *
     * @return void
     */
    public function handleBulk(array $transfers, $eventName)
    {
        $this->getFacade()->writeCollectionByIdMerchantEvents($transfers);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            MerchantEvents::ENTITY_SPY_MERCHANT_UPDATE,
        ];
    }
}
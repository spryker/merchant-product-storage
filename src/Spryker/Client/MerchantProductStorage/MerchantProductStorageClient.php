<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\MerchantProductStorage;

use Generated\Shared\Transfer\MerchantProductStorageTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \Spryker\Client\MerchantProductStorage\MerchantProductStorageFactory getFactory()
 */
class MerchantProductStorageClient extends AbstractClient implements MerchantProductStorageClientInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idProductAbstract
     *
     * @return \Generated\Shared\Transfer\MerchantProductStorageTransfer|null
     */
    public function findOne(int $idProductAbstract): ?MerchantProductStorageTransfer
    {
        return $this->getFactory()
            ->createMerchantProductStorageReader()
            ->findOne($idProductAbstract);
    }
}

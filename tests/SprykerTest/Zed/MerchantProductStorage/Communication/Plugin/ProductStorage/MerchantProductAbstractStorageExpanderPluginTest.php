<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace SprykerTest\Zed\MerchantProductStorage\Communication\Plugin\ProductStorage;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\MerchantProductTransfer;
use Generated\Shared\Transfer\MerchantTransfer;
use Generated\Shared\Transfer\ProductAbstractStorageTransfer;
use ReflectionClass;
use Spryker\Zed\MerchantProductStorage\Communication\Plugin\ProductStorage\MerchantProductAbstractStorageExpanderPlugin;
use SprykerTest\Zed\MerchantProductStorage\MerchantProductStorageTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group MerchantProductStorage
 * @group Communication
 * @group Plugin
 * @group ProductStorage
 * @group MerchantProductAbstractStorageExpanderPluginTest
 * Add your own group annotations below this line
 */
class MerchantProductAbstractStorageExpanderPluginTest extends Unit
{
    /**
     * @uses \Spryker\Zed\MerchantProductStorage\Communication\Plugin\ProductStorage\MerchantProductAbstractStorageExpanderPlugin::NEGATIVE_CACHE
     */
    protected const string NEGATIVE_CACHE = '';

    protected MerchantProductStorageTester $tester;

    public function setUp(): void
    {
        parent::setUp();

        $this->clearPluginCache();
    }

    /**
     * @dataProvider expandDataProvider
     */
    public function testExpandSetsMerchantReferenceOnTransfer(
        bool $hasMerchant,
        bool $isMerchantActive,
        bool $expectedMerchantReference,
    ): void {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $idProductAbstract = $productConcreteTransfer->getFkProductAbstract();

        $merchantTransfer = null;

        if ($hasMerchant) {
            $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => $isMerchantActive]);
            $this->tester->haveMerchantProduct([
                MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchantOrFail(),
                MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $idProductAbstract,
            ]);
        }

        $storageTransfer = (new ProductAbstractStorageTransfer())->setIdProductAbstract($idProductAbstract);
        $plugin = new MerchantProductAbstractStorageExpanderPlugin();

        // Act
        $result = $plugin->expand($storageTransfer);

        // Assert — verify transfer
        if ($expectedMerchantReference) {
            $this->assertSame($merchantTransfer->getMerchantReference(), $result->getMerchantReference());
        } else {
            $this->assertNull($result->getMerchantReference());
        }

        // Assert — verify cache entry is written correctly
        $cache = $this->getPluginCache();

        $this->assertArrayHasKey($idProductAbstract, $cache);

        if ($expectedMerchantReference) {
            $this->assertSame($merchantTransfer->getMerchantReference(), $cache[$idProductAbstract]);
        } else {
            $this->assertSame(static::NEGATIVE_CACHE, $cache[$idProductAbstract]);
        }
    }

    /**
     * @return array<string, array<mixed>>
     */
    public static function expandDataProvider(): array
    {
        return [
            'active merchant linked sets merchant reference and caches it' => [
                'hasMerchant' => true,
                'isMerchantActive' => true,
                'expectedMerchantReference' => true,
            ],
            'inactive merchant linked leaves transfer unchanged and writes negative cache' => [
                'hasMerchant' => true,
                'isMerchantActive' => false,
                'expectedMerchantReference' => false,
            ],
            'no merchant linked leaves transfer unchanged and writes negative cache' => [
                'hasMerchant' => false,
                'isMerchantActive' => false,
                'expectedMerchantReference' => false,
            ],
        ];
    }

    public function testExpandReadsFromCacheOnSubsequentCall(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $idProductAbstract = $productConcreteTransfer->getFkProductAbstract();

        $merchantTransfer = $this->tester->haveMerchant([MerchantTransfer::IS_ACTIVE => true]);
        $this->tester->haveMerchantProduct([
            MerchantProductTransfer::ID_MERCHANT => $merchantTransfer->getIdMerchantOrFail(),
            MerchantProductTransfer::ID_PRODUCT_ABSTRACT => $idProductAbstract,
        ]);

        $plugin = new MerchantProductAbstractStorageExpanderPlugin();

        $plugin->expand((new ProductAbstractStorageTransfer())->setIdProductAbstract($idProductAbstract));

        $cacheAfterFirstExpand = $this->getPluginCache();

        // Act: expand again with the same product abstract
        $result = $plugin->expand((new ProductAbstractStorageTransfer())->setIdProductAbstract($idProductAbstract));

        // Assert: result is correct and cache is byte-for-byte unchanged (no re-fetch)
        $this->assertSame($merchantTransfer->getMerchantReference(), $result->getMerchantReference());
        $this->assertEquals($cacheAfterFirstExpand, $this->getPluginCache());
    }

    /**
     * @return array<int|string, string|null>
     */
    protected function getPluginCache(): array
    {
        $reflection = new ReflectionClass(MerchantProductAbstractStorageExpanderPlugin::class);

        return $reflection->getProperty('productToMerchantCache')->getValue();
    }

    protected function clearPluginCache(): void
    {
        $reflection = new ReflectionClass(MerchantProductAbstractStorageExpanderPlugin::class);
        $reflection->getProperty('productToMerchantCache')->setValue(null, []);
    }
}

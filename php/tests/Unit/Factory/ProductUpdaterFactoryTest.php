<?php

declare(strict_types=1);

namespace Tests\Unit\Factory;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Factory\ProductUpdaterFactory;
use GildedRose\Models\Item;
use PHPUnit\Framework\TestCase;

class ProductUpdaterFactoryTest extends TestCase
{
    public function testGetUpdater(): void
    {
        $item = new Item('foo', 0, 0);
        $updater = ProductUpdaterFactory::getUpdater($item);
        $this->assertInstanceOf(ProductUpdater::class, $updater);
    }
    public function testGetAgedBrie(): void
    {
        $item = new Item('Aged brie', 0, 0);
        $updater = ProductUpdaterFactory::getUpdater($item);
        $this->assertInstanceOf(ProductUpdater::class, $updater);
    }
    public function testGetBackstagePasses(): void
    {
        $item = new Item('Backstage passes', 0, 0);
        $updater = ProductUpdaterFactory::getUpdater($item);
        $this->assertInstanceOf(ProductUpdater::class, $updater);
    }
    public function testGetConjured(): void
    {
        $item = new Item('Conjured', 0, 0);
        $updater = ProductUpdaterFactory::getUpdater($item);
        $this->assertInstanceOf(ProductUpdater::class, $updater);
    }
    public function testGetSulfuras(): void
    {
        $item = new Item('Sulfuras', 0, 0);
        $updater = ProductUpdaterFactory::getUpdater($item);
        $this->assertInstanceOf(ProductUpdater::class, $updater);
    }

}
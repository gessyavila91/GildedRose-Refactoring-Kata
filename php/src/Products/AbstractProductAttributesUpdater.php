<?php

declare(strict_types=1);

namespace GildedRose\Products;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Models\Item;

abstract class AbstractProductAttributesUpdater implements ProductUpdater
{
    public static function getMinQuality(): int
    {
        return 0;
    }

    public static function increaseQuality(Item $item, int $amount = 1): void
    {
        $item->quality = min($item->quality + $amount, self::getMaxQuality());
    }

    public static function decreaseQuality(Item $item, int $amount = 1): void
    {
        $item->quality = max($item->quality - $amount, self::getMinQuality());
    }

    public static function decreaseSellIn(Item $item): void
    {
        $item->sellIn--;
    }

    public static function resetQuality(Item $item): void
    {
        $item->quality = 0;
    }

    public static function isExpired(Item $item): bool
    {
        return $item->sellIn < 0;
    }

    private static function getMaxQuality(): int
    {
        return 50;
    }
}

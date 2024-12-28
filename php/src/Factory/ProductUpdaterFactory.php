<?php

declare(strict_types=1);

namespace GildedRose\Factory;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Models\Item;
use GildedRose\Products\{
    AgedBrieAttributesUpdater,
    BackstagePassesAttributesUpdater,
    ConjuredAttributesUpdater,
    RegularAttributesUpdater,
    SulfurasAttributesUpdater
};

class ProductUpdaterFactory
{
    public static function getUpdater(Item $item): ProductUpdater
    {
        $name = strtoupper($item->name);

        return match (true) {
            str_contains($name, 'AGED BRIE') => new AgedBrieAttributesUpdater(),
            str_contains($name, 'BACKSTAGE PASSES') => new BackstagePassesAttributesUpdater(),
            str_contains($name, 'CONJURED') => new ConjuredAttributesUpdater(),
            str_contains($name, 'SULFURAS') => new SulfurasAttributesUpdater(),
            default => new RegularAttributesUpdater(),
        };
    }
}

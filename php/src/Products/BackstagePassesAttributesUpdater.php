<?php

declare(strict_types=1);

namespace GildedRose\Products;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Models\Item;

class BackstagePassesAttributesUpdater extends AbstractProductAttributesUpdater implements ProductUpdater
{
    private int $qualityUpdateAmount = 1;

    public function update(Item $item): void
    {
        $this->decreaseSellIn($item);

        if ($this->isExpired($item)) {
            $this->resetQuality($item);
            return;
        }

        $this->increaseQuality($item, $this->getQualityUpdateAmount($item));
    }

    public function getQualityUpdateAmount(Item $item): int
    {
        return $this->qualityUpdateAmount + ($item->sellIn < 11) + ($item->sellIn < 6);
    }
}

<?php

declare(strict_types=1);

namespace GildedRose\Products;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Models\Item;

class RegularAttributesUpdater extends AbstractProductAttributesUpdater implements ProductUpdater
{
    private int $qualityUpdateAmount = 1;

    public function update(Item $item): void
    {
        $this->decreaseSellIn($item);

        $item->sellIn < 0 ?
            $this->decreaseQuality($item, $this->qualityUpdateAmount * 2) :
            $this->decreaseQuality($item, $this->qualityUpdateAmount);
    }
}

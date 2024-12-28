<?php

declare(strict_types=1);

namespace GildedRose\Products;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Models\Item;

class ConjuredAttributesUpdater extends AbstractProductAttributesUpdater implements ProductUpdater
{
    private int $qualityUpdateAmount = 1;

    public function update(Item $item): void
    {
        $this->decreaseSellIn($item);
        $this->decreaseQuality($item, $this->qualityUpdateAmount);
        $this->decreaseQuality($item, $this->qualityUpdateAmount);

        if ($this->isExpired($item)) {
            $this->decreaseQuality($item, $this->qualityUpdateAmount);
            $this->decreaseQuality($item, $this->qualityUpdateAmount);
        }
    }
}

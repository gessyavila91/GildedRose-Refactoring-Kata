<?php

declare(strict_types=1);

namespace GildedRose;

use GildedRose\Factory\ProductUpdaterFactory;
use GildedRose\Models\Item;

final class GildedRose
{
    /**
     * @param Item[] $items
     */
    public function __construct(
        private array $items
    ) {
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $updater = ProductUpdaterFactory::getUpdater($item);
            $updater->update($item);
        }
    }
}

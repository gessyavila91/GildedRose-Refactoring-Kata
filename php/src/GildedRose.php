<?php

declare(strict_types=1);

namespace GildedRose;

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
            $productType = $this->getProductTypeByName($item);
            switch ($productType) {
                case 'AGED BRIE':
                    $this->agedBrieUpdate($item);
                    break;
                case 'BACKSTAGE PASSES':
                    $this->backstagePassesUpdate($item);
                    break;
                case 'CONJURED':
                    $this->conjuredUpdate($item);
                    break;
                case 'REGULAR':
                    $this->regularUpdate($item);
                    break;
                case 'SULFURAS':
                    $this->sulfurasUpdate($item);
                    break;
            }
        }
    }

    public function getProductTypeByName(Item $item): string
    {
        $name = strtoupper($item->name);

        if (str_contains($name, 'AGED BRIE')) {
            return 'AGED BRIE';
        }

        if (str_contains($name, 'BACKSTAGE PASSES')) {
            return 'BACKSTAGE PASSES';
        }

        if (str_contains($name, 'CONJURED')) {
            return 'CONJURED';
        }

        if (str_contains($name, 'SULFURAS')) {
            return 'SULFURAS';
        }

        return 'REGULAR';
    }

    private function increaseQuality(Item $item): void
    {
        if ($item->quality < 50) {
            $item->quality++;
        }
    }

    private function decreaseQuality(Item $item): void
    {
        if ($item->quality > 0) {
            $item->quality--;
        } elseif ($item->quality < 0) {
            $item->quality = 0;
        }
    }

    private function decreaseSellIn(Item $item): void
    {
        if ($this->getProductTypeByName($item) !== 'SULFURAS') {
            $item->sellIn--;
        }
    }

    private function agedBrieUpdate(Item $item): void
    {
        $this->decreaseSellIn($item);
        $this->increaseQuality($item);
        if ($item->sellIn < 0) {
            $this->increaseQuality($item);
        }
    }

    private function backstagePassesUpdate(Item $item): void
    {
        $this->decreaseSellIn($item);
        $this->increaseQuality($item);
        if ($item->sellIn < 0) {
            $item->quality = 0;
            return;
        }
        if ($item->sellIn < 11) {
            $this->increaseQuality($item);
        }
        if ($item->sellIn < 6) {
            $this->increaseQuality($item);
        }
    }

    private function conjuredUpdate(Item $item): void
    {
        $this->decreaseSellIn($item);

        $this->decreaseQuality($item);
        $this->decreaseQuality($item);
        if ($item->sellIn < 0) {
            $this->decreaseQuality($item);
            $this->decreaseQuality($item);
        }
    }

    private function regularUpdate(Item $item): void
    {
        $this->decreaseSellIn($item);

        $this->decreaseQuality($item);
        if ($item->sellIn < 0) {
            $this->decreaseQuality($item);
        }
    }

    private function sulfurasUpdate(Item $item): void
    {
        // yeah dont do nothing 😆
    }
}

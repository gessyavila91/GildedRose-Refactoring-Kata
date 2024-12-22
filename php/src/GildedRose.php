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

    public function updateQuality(): void
    {
        foreach ($this->items as $item) {
            $productType = $this->productType($item);
            switch ($productType) {
                case 'SULFURAS':
                    $this->sulfurasUpdate($item);
                    break;
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
            }
        }
    }

    public function increaseBackstagePassesQuality(Item $item): void
    {
        $productType = $this->productType($item);

        if ($item->sellIn < 11 and $item->quality < 50 and
            $productType === 'BACKSTAGE PASSES') {
            $this->increaseQuality($item);
        }
        if ($item->sellIn < 6 and $item->quality < 50 and
            $productType === 'BACKSTAGE PASSES') {
            $this->increaseQuality($item);
        }
    }

    public function productType(Item $item): string
    {
        $name = strtoupper($item->name);

        if (str_contains($name, 'AGED BRIE')) {
            return 'AGED BRIE';
        }

        if (str_contains($name, 'BACKSTAGE PASSES')) {
            return 'BACKSTAGE PASSES';
        }

        if (str_contains($name, 'SULFURAS')) {
            return 'SULFURAS';
        }

        if (str_contains($name, 'CONJURED')) {
            return 'CONJURED';
        }

        return 'REGULAR';
    }

    private function increaseQuality(Item $item): void
    {
        $item->quality++;
    }

    private function decreaseQuality(Item $item): void
    {
        if ($this->productType($item) !== 'SULFURAS') {
            $item->quality--;
        }
    }

    private function decreaseSellIn(Item $item): void
    {
        if ($this->productType($item) !== 'SULFURAS') {
            $item->sellIn--;
        }
    }

    private function sulfurasUpdate(Item $item): void
    {
        // yeah dont do nothing 😆
    }

    private function agedBrieUpdate(Item $item): void
    {
        $this->decreaseSellIn($item);
        $this->increaseQuality($item);
        if ($item->sellIn < 0 and $item->quality < 50) {
            $this->increaseQuality($item);
        }
    }

    private function backstagePassesUpdate(Item $item): void
    {
        $this->decreaseSellIn($item);
        $this->increaseQuality($item);
        $this->increaseBackstagePassesQuality($item);
        if ($item->sellIn < 0) {
            $item->quality = 0;
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
        if ($item->quality > 0) {
            $this->decreaseQuality($item);
            if ($item->sellIn < 0) {
                $this->decreaseQuality($item);
            }
        }
    }
}

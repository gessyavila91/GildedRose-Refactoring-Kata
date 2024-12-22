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

    public function updateQuality(): void {
        foreach ($this->items as $item) {
            $productType = $this->productType($item);
            //////
            if ($productType != 'AGED BRIE' and
                $productType != 'BACKSTAGE PASSES' and
                $item->quality > 0) {

                $this->decreaseQuality($item);
            } elseif ($item->quality < 50) {
                $this->increaseQuality($item);
                $this->increaseBackstagePassesQuality($item);
            }

            $this->decreaseSellIn($item);

            if ($item->sellIn < 0) {
                if ($productType != 'AGED BRIE') {
                    if ($productType != 'BACKSTAGE PASSES' and
                        $item->quality > 0) {

                        $this->decreaseQuality($item);
                    } else {
                        $item->quality -= $item->quality;
                    }
                } elseif ($item->quality < 50) {
                    $this->increaseQuality($item);
                }
            }
        }
    }

    /**
     * @param Item $item
     * @return void
     */
    private function increaseQuality(Item $item): void {
        $item->quality++;
    }
    /**
     * @param Item $item
     * @return void
     */
    public function increaseBackstagePassesQuality(Item $item): void {
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

    /**
     * @param Item $item
     * @return void
     */
    private function decreaseQuality(Item $item): void {
        if ($this->productType($item) != 'SULFURAS') {
            $item->quality--;
        }
    }

    /**
     * @param Item $item
     * @return void
     */
    private function decreaseSellIn(Item $item): void {
        if ($this->productType($item) != 'SULFURAS') {
            $item->sellIn--;
        }
    }

    /**
     * @param Item $item
     * @return string
     */
    public function productType(Item $item): string {
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
}

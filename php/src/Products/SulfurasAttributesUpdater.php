<?php

declare(strict_types=1);

namespace GildedRose\Products;

use GildedRose\Contracts\ProductUpdater;
use GildedRose\Models\Item;

class SulfurasAttributesUpdater extends AbstractProductAttributesUpdater implements ProductUpdater
{
    public function update(Item $item): void
    {
        // Lógica de actualización específica para Aged Brie.
    }
}

<?php

declare(strict_types=1);

namespace GildedRose\Contracts;

use GildedRose\Models\Item;

interface ProductUpdater
{
    public function update(Item $item): void;
}

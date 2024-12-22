<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function testFoo(): void
    {
        $items = [
            new Item('foo', 0, 0),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        $this->assertSame('foo', $items[0]->name);
        $this->assertSame(-1, $items[0]->sellIn); // 1.1
        $this->assertSame(0, $items[0]->quality); // 2.2 calidad nunca negativa
    }

    public function testCoreRules(): void
    {
        $items = [
            new Item('+5 Agility Vest', 0, 20),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        //2.1 Tras la fecha de venta, la calidad se degrada al doble de velocidad.
        $this->assertSame(18, $items[0]->quality);
    }

    public function testAgedBrie(): void
    {
        $items = [
            new Item('Aged Brie', 2, 0),
            new Item('Aged Brie', 0, 0),
            new Item('Aged Brie', 0, 49),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        //2.3.i Aged Brie incrementa su calidad diariamente en 1
        $this->assertSame(1, $items[0]->quality);
        //2.3.ii Aumenta el doble por día tras expirar la fecha de venta
        $this->assertSame(2, $items[1]->quality);
        //2.4 La calidad de un artículo (Quality) no puede superar 50
        $this->assertSame(50, $items[2]->quality);
    }

    public function testBackstagePasses(): void
    {
        $items = [
            new Item('Backstage passes to a TAFKAL80ETC concert', 15, 20),
            new Item('Backstage passes to a TAFKAL80ETC concert', 10, 40),
            new Item('Backstage passes to a TAFKAL80ETC concert', 5, 30),
            new Item('Backstage passes to a TAFKAL80ETC concert', 0, 30),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        //2.6 Backstage passes incrementan su calidad al acercarse el concierto
        $this->assertSame(21, $items[0]->quality);
        //2.6.i +2 calidad si faltan 10 días o menos
        $this->assertSame(42, $items[1]->quality);
        //2.6.ii +3 calidad si faltan 5 días o menos
        $this->assertSame(33, $items[2]->quality);
        //2.6.iii Calidad cae a 0 tras el concierto
        $this->assertSame(0, $items[3]->quality);
    }

    public function testSulfuras(): void
    {
        $items = [
            new Item('Sulfuras, Hand of Ragnaros', 0, 80),
            new Item('Sulfuras, Hand of Ragnaros', -1, 80),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        // Sulfuras es un artículo legendario
        //2.5.1 No tiene fecha de venta
        $this->assertSame(0, $items[0]->sellIn);
        //2.5.2 No se degrada en calidad
        $this->assertSame(80, $items[0]->quality);
        //2.5.1 No tiene fecha de venta
        $this->assertSame(-1, $items[1]->sellIn);
        //2.5.2 No se degrada en calidad
        $this->assertSame(80, $items[1]->quality);
    }

    public function testConjured(): void
    {
        $items = [
            new Item('Conjured Mana Cake', 3, 6),
            new Item('Conjured Mana Cake', 0, 6),
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        // Sulfuras es un artículo legendario
        //3.1 Los artículos conjurados (Conjured) degradan su calidad (Quality) el doble de rápido que los artículos normales
        $this->assertSame(4, $items[0]->quality);
        $this->assertSame(2, $items[1]->quality);
    }

    public function testProductType(): void
    {
        $items = [
            // 'REGULAR'
            new Item('+5 Dexterity Vest', 10, 20),
            // 'SULFURAS', 'AGED BRIE', 'CONJURED', 'BACKSTAGE PASSES'
            new Item('Aged Brie', 2, 0),
            new Item('Sulfuras, Hand of Ragnaros', -1, 80),
            new Item('Backstage passes to a TAFKAL80ETC concert', 15, 20),
            new Item('Conjured Mana Cake', 3, 6),
        ];

        $gildedRose = new GildedRose($items);

        $this->assertSame('AGED BRIE', $gildedRose->productType($items[1]));
        $this->assertSame('REGULAR', $gildedRose->productType($items[0]));
        $this->assertSame('SULFURAS', $gildedRose->productType($items[2]));
        $this->assertSame('BACKSTAGE PASSES', $gildedRose->productType($items[3]));
        $this->assertSame('CONJURED', $gildedRose->productType($items[4]));
    }
}

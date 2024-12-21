<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    //keywords 'Sulfuras', 'Aged Brie', 'Conjured', 'Backstage passes'

    public function testFoo(): void {
         $items = [
            new Item('foo', 0, 0),// Indx 00
        ];
        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        $this->assertSame('foo', $items[0]->name);
        $this->assertSame(-1, $items[0]->sellIn);// 1.1
        $this->assertSame(0, $items[0]->quality);// 2.2 calidad nunca negativa
    }

    public function testCoreRules(): void {
        $items = [
            new Item('+5 Dexterity Vest', 0, 20),// Indx 00
            new Item('+5 Agility Vest', 0, 20),// Indx 01
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();
        //2.1 Tras la fecha de venta, la calidad se degrada al doble de velocidad.
        $this->assertSame(18, $items[1]->quality);
    }

    public function testAgedBrie(): void {
        $items = [
            new Item('Aged Brie', 2, 0),// Indx 00
            new Item('Aged Brie', 0, 0),// Indx 01
            new Item('Aged Brie', 0, 49),// Indx 02
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

    public function testBackstagePasses(): void {
        $items = [
            new Item('Backstage passes to a TAFKAL80ETC concert', 15, 20),// Indx 00
            new Item('Backstage passes to a TAFKAL80ETC concert', 10, 40),// Indx 01
            new Item('Backstage passes to a TAFKAL80ETC concert', 5, 30),// Indx 02
            new Item('Backstage passes to a TAFKAL80ETC concert', 0, 30),// Indx 03
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

    public function testSulfuras(): void {
        $items = [
            new Item('Sulfuras, Hand of Ragnaros', 0, 80),// Indx 00
            new Item('Sulfuras, Hand of Ragnaros', -1, 80),// Indx 01
        ];

        $gildedRose = new GildedRose($items);
        $gildedRose->updateQuality();

        // Sulfuras es un artículo legendario
        //2.5.i No tiene fecha de venta
        $this->assertSame(0, $items[0]->sellIn);
        //2.5.ii No se degrada en calidad
        $this->assertSame(80, $items[0]->quality);
        //2.5.i No tiene fecha de venta
        $this->assertSame(-1, $items[1]->sellIn);
        //2.5.ii No se degrada en calidad
        $this->assertSame(80, $items[1]->quality);
    }
}

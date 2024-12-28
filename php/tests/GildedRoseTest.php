<?php

declare(strict_types=1);

namespace Tests;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    private GildedRose $gildedRose;

    protected function setUp(): void
    {
        $this->gildedRose = new GildedRose([]);
    }

    public function testCoreRules(): void
    {
        $items = [
            new Item('+8 Agility Helmet', 0, 20),
            new Item('+5 Dexterity Vest', 5, 0),
            new Item('+7 Intelligence Cloak', -10, 30),
            new Item('+3 Strength Boots', -5, 0),
            new Item('+1 Perception Goggles', -10, 1),
            new Item('+6 Resistance Shield', 8, -1),
        ];

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

        // 2.1 Tras la fecha de venta, la calidad se degrada al doble de velocidad.
        // +8 Agility Helmet
        $this->assertSame('+8 Agility Helmet', $items[0]->name);
        $this->assertSame(18, $items[0]->quality);
        // +5 Dexterity Vest
        $this->assertSame(4, $items[1]->sellIn); // 1.3
        $this->assertSame(0, $items[1]->quality); // 2.2 calidad nunca negativa
        // +7 Intelligence Cloak
        $this->assertSame(-11, $items[2]->sellIn); // 1.1
        $this->assertSame(28, $items[2]->quality); // 2.2 calidad nunca negativa
        // +3 Strength Boots
        $this->assertSame(-6, $items[3]->sellIn); // 1.1
        $this->assertSame(0, $items[3]->quality); // 2.2 calidad nunca negativa
        // +1 Perception Goggles
        $this->assertSame(-11, $items[4]->sellIn); // 1.1
        $this->assertSame(0, $items[4]->quality); // 2.2 calidad nunca negativa
        // +6 Resistance Shield
        $this->assertSame(7, $items[5]->sellIn); // 1.1
        $this->assertSame(0, $items[5]->quality); // 2.2 calidad nunca negativa
    }

    public function testAgedBrie(): void
    {
        $items = [
            new Item('Aged Brie', 2, 0),
            new Item('Aged Brie', 0, 0),
            new Item('Aged Brie', 0, 49),
            new Item('Aged Brie', 0, 50),
            new Item('Aged Brie', -1, 50),
        ];

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

        // Aged Brie,2,0
        //2.3.i Aged Brie incrementa su calidad diariamente en 1
        $this->assertSame(1, $items[0]->quality);
        // Aged Brie,0,0
        //2.3.ii Aumenta el doble por día tras expirar la fecha de venta
        $this->assertSame(2, $items[1]->quality);
        // Aged Brie,0,49
        //2.4 La calidad de un artículo (Quality) no puede superar 50
        $this->assertSame(50, $items[2]->quality);
        // Aged Brie,0,50
        //2.4 La calidad de un artículo (Quality) no puede superar 50
        $this->assertSame(50, $items[3]->quality);
        // Aged Brie,-1,50
        //2.4 La calidad de un artículo (Quality) no puede superar 50
        $this->assertSame(50, $items[4]->quality);
    }

    public function backstagePassesProvider(): array
    {
        return [
            new Item('Backstage passes to Fiesta de Luna Negra', 15, 20),
            new Item('Backstage passes to Concierto de Jaina y los Elementales', 10, 40),
            new Item('Backstage passes to Torneo de los Campeones Argent', 5, 30),
            new Item('Backstage passes to El Baile del Rey Lich', 0, 30),
            new Item('Backstage passes to Festival de Prontera', 5, 49),
            new Item('Backstage passes to Feria de Payon', 5, 50),
            new Item('Backstage passes to Ceremonia de la Diosa Freya', -1, 25),
        ];
    }

    public function testBackstagePassesIncrementBeforeConcert(): void
    {
        $items = $this->backstagePassesProvider();

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

        // 2.6 Backstage passes incrementan su calidad al acercarse el concierto
        $this->assertSame(21, $items[0]->quality); // Fiesta de Luna Negra
        // 2.6.i +2 calidad si faltan 10 días o menos
        $this->assertSame(42, $items[1]->quality); // Concierto de Jaina
        // 2.6.ii +3 calidad si faltan 5 días o menos
        $this->assertSame(33, $items[2]->quality); // Torneo de los Campeones

        // 2.4 BP 5,49 => Max quality capped at 50
        $this->assertSame(50, $items[4]->quality); // Festival de Prontera
        // 2.4 BP 5,50 => Already at max quality
        $this->assertSame(50, $items[5]->quality); // Feria de Payon
    }

    public function testBackstagePasses(): void
    {
        $items = $this->backstagePassesProvider();

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

        // 2.6 Backstage passes incrementan su calidad al acercarse el concierto
        $this->assertSame(21, $items[0]->quality); // Fiesta de Luna Negra
        // 2.6.i +2 calidad si faltan 10 días o menos
        $this->assertSame(42, $items[1]->quality); // Concierto de Jaina
        // 2.6.ii +3 calidad si faltan 5 días o menos
        $this->assertSame(33, $items[2]->quality); // Torneo de los Campeones

        // BP 5,49 => Max quality capped at 50
        $this->assertSame(50, $items[4]->quality); // Festival de Prontera
        // BP 5,50 => Already at max quality
        $this->assertSame(50, $items[5]->quality); // Feria de Payon
    }

    public function testBackstagePassesQualityDriopsToZero(): void
    {
        $items = $this->backstagePassesProvider();

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();
        // 2.6.iii Calidad cae a 0 tras el concierto
        $this->assertSame(0, $items[3]->quality); // Baile del Rey Lich
        // BP -1,25 => Expired, quality drops to 0
        $this->assertSame(0, $items[6]->quality); // Ceremonia de Freya
    }

    public function testSulfuras(): void
    {
        $items = [
            new Item('Sulfuras, Hand of Ragnaros', 0, 80),
            new Item('Sulfuras, Hammer of Ragnaros', -1, 80),
        ];

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

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
            new Item('Conjured Fell Potion', 0, 6),
        ];

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

        // Sulfuras es un artículo legendario
        // 3.1 Los artículos conjurados (Conjured) degradan su calidad (Quality) el doble de rápido que los artículos normales
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

        $this->gildedRose->setItems($items);
        $this->gildedRose->updateQuality();

        $this->assertSame('AGED BRIE', $this->gildedRose->getProductTypeByName($items[1]));
        $this->assertSame('BACKSTAGE PASSES', $this->gildedRose->getProductTypeByName($items[3]));
        $this->assertSame('CONJURED', $this->gildedRose->getProductTypeByName($items[4]));
        $this->assertSame('REGULAR', $this->gildedRose->getProductTypeByName($items[0]));
        $this->assertSame('SULFURAS', $this->gildedRose->getProductTypeByName($items[2]));
    }
}

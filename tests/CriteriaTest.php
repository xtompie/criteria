<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Xtompie\Criteria\Criteria;

class CriteriaTest extends TestCase
{
    public function testToArray()
    {
        // given
        $criteria = new Criteria(where: ['a' => 'b'], order: 'c', limit: 10, offset: 20);
        // when
        $array = $criteria->toArray();
        // then
        $this->assertSame(['where' => ['a' => 'b'], 'order' => 'c', 'limit' => 10, 'offset' => 20], $array);
    }

    public function testToFlatArray()
    {
        // given
        $criteria = new Criteria(where: ['a' => 'b'], order: 'c', limit: 10, offset: 20);
        // when
        $array = $criteria->toFlatArray();
        // then
        $this->assertSame(['a' => 'b', 'order' => 'c', 'limit' => 10, 'offset' => 20], $array);
    }

    public function testInputOrderField()
    {
        // when
        $criteria = Criteria::ofInput(['sort' => 'a'], orderField: 'sort');
        // then
        $this->assertSame('a', $criteria->order());
    }

    public function testInputOrderFieldNotExists()
    {
        // when
        $criteria = Criteria::ofInput(['a' => 'a'], orderField: 'sort');
        // then
        $this->assertSame(null, $criteria->order());
    }

    public function testInputOrderOptionsPositive()
    {
        // when
        $criteria = Criteria::ofInput(['sort' => 'a'], orderOptions: ['a', 'b']);
        // then
        $this->assertSame('a', $criteria->order());
    }

    public function testInputOrderOptionsNegative()
    {
        // when
        $criteria = Criteria::ofInput(['sort' => 'c'], orderOptions: ['a', 'b']);
        // then
        $this->assertSame(null, $criteria->order());
    }

    public function testInputOrderDefault()
    {
        // when
        $criteria = Criteria::ofInput(['sort' => 'c'], orderOptions: ['a', 'b'], orderDefault: 'd');
        // then
        $this->assertSame('d', $criteria->order());
    }

    public function testInputOffset()
    {
        // when
        $criteria = Criteria::ofInput(['page' => '4', 'perpage' => '10']);
        // then
        $this->assertSame(30, $criteria->offset());
    }

}

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
}

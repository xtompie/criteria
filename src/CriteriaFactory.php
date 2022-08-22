<?php

declare(strict_types=1);

namespace Xtompie\Criteria;

class CriteriaFactory
{
    public static function input(
        array $input,

        ?string $orderField = 'sort',
        ?array $orderOptions = [],
        ?string $orderDefault = null,

        ?string $pageField = 'page',
        ?int $pageDefault = null,

        ?string $perpageField = 'perpage',
        ?int $perpageMin = null,
        ?int $perpageMax = null,
        ?array $perpageOptions = [],
        ?int $perpageDefault = null,

        ?array $whereValid = [],

    ): Criteria
    {
        $order = $input[$orderField] ?? null;
        unset($input[$orderField]);
        if ($orderOptions && !in_array($order, $orderOptions)) {
            $order = null;
        }
        if ($order === null) {
            $order = $orderDefault;
        }

        $page = $input[$pageField] ?? $pageDefault;
        unset($input[$pageField]);

        $perpage = $input[$perpageField] ?? null;
        unset($input[$perpageField]);
        if ($perpage !== null && $perpageMin !== null && $perpage < $perpageMin) {
            $perpage = null;
        }
        if ($perpage !== null && $perpageMax !== null && $perpage > $perpageMax) {
            $perpage = null;
        }
        if ($perpage !== null && $perpageOptions !== null && !in_array($perpage, $perpageOptions)) {
            $perpage = null;
        }
        if ($perpage === null && $perpageDefault !== null) {
            $perpage = $perpageDefault;
        }

        if ($whereValid != null) {
            $input = array_filter($input, fn ($key) => in_array($key, $whereValid), ARRAY_FILTER_USE_KEY);
        }

        $criteria = new Criteria($input, $order);
        if ($perpage !== null) {
            $criteria = $criteria->paginate((int)$page, (int)$perpage);
        }
        return $criteria;
    }
}

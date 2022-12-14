<?php

declare(strict_types=1);

namespace Xtompie\Criteria;

use Exception;

class Criteria
{
    public static function ofEmpty(): static
    {
        return new static();
    }

    public static function of(?array $where = null, ?string $order = null, ?int $limit = null, ?int $offset = null): static
    {
        return new static($where, $order, $limit, $offset);
    }

    public static function ofInput(
        array $input,
        ?string $orderField = 'sort',
        ?array $orderOptions = null,
        ?string $orderDefault = null,
        ?string $pageField = 'page',
        ?int $pageDefault = null,
        ?string $perpageField = 'perpage',
        ?int $perpageMin = null,
        ?int $perpageMax = null,
        ?array $perpageOptions = null,
        ?int $perpageDefault = null,
        ?array $whereValid = null,
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

        $page = isset($input[$pageField]) ? (int)$input[$pageField] : $pageDefault;
        unset($input[$pageField]);

        $perpage = isset($input[$perpageField]) ? (int)$input[$perpageField] : null;
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

    public function __construct(
        protected ?array $where = null,
        protected ?string $order = null,
        protected ?int $limit = null,
        protected ?int $offset = null,
    ) {
        if ($this->limit !== null && !($this->limit > 0)) {
            throw new Exception();
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'where' => array_filter($this->where()),
            'order' => $this->order(),
            'limit' => $this->limit(),
            'offset' => $this->offset(),
        ]);
    }

    public function toFlatArray()
    {
        return array_filter(array_merge(
            array_filter($this->where()),
            [
                'order' => $this->order(),
                'limit' => $this->limit(),
                'offset' => $this->offset(),
            ]
        ));
    }

    public function order(): ?string
    {
        return isset($this->order) ? $this->order : null;
    }

    public function withOrder(?string $order): static
    {
        $new = clone $this;
        $new->order = $order;
        return $new;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function withOffset(?int $offset): static
    {
        $new = clone $this;
        $new->offset = $offset;
        return $new;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function withLimit(?int $limit): static
    {
        if ($this->limit !== null && !($this->limit > 0)) {
            throw new Exception();
        }

        $new = clone $this;
        $new->limit = $limit;
        return $new;
    }

    public function where(): ?array
    {
        return $this->where;
    }

    public function withWhere(?array $where): static
    {
        $new = clone $this;
        $new->where = $where;
        return $new;
    }

    public function without(string $key): static
    {
        $new = clone $this;
        unset($new->where[$key]);
        return $new;
    }

    public function merge(Criteria $criteria): static
    {
        return new static(
            array_merge((array)$this->where(), (array)$criteria->where()),
            $criteria->order() != null ? $criteria->order() : $this->order(),
            $criteria->limit() != null ? $criteria->limit() : $this->limit(),
            $criteria->offset() != null ? $criteria->offset() : $this->offset(),
        );
    }

    public function has(string $key): bool
    {
        return isset($this->where[$key]);
    }

    public function get(string $key): mixed
    {
        return isset($this->where[$key]) ? $this->where[$key] : null;
    }

    public function filterToArray(?callable $callback = null): array
    {
        return array_filter($this->where, $callback, ARRAY_FILTER_USE_BOTH);
    }

    public function filter(?callable $callback = null): static
    {
        return new static(array_filter($this->where, $callback, ARRAY_FILTER_USE_BOTH), $this->order, $this->limit, $this->offset);
    }

    public function only(array $keys): static
    {
        return $this->filter(fn ($v, $k) => in_array($k, $keys));
    }

    public function onlyWhere(): static
    {
        return new static((array)$this->where(), $this->order, $this->limit, $this->offset);
    }

    public function reject(array $keys): static
    {
        return $this->filter(fn ($v, $k) => !in_array($k, $keys));
    }

    public function paginate(int $page, int $perPage): static
    {
        $page = max($page - 1, 0);
        return $this->withOffset($perPage * $page)->withLimit($perPage);
    }

    public function page(): ?int
    {
        if ($this->offset() === null || $this->limit() === null || $this->limit() === 0) {
            return null;
        }

        return (int)($this->offset() / $this->limit());
    }
}

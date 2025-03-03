<?php

declare(strict_types=1);

namespace Kartalit\Services;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Kartalit\Interfaces\ModelInterface;

class PaginatorService extends Paginator
{
    public function toArray(string|null $method = null): array
    {
        $result = [];
        foreach ($this as $item) {
            /** @var ModelInterface $item */
            if (
                $method !== null and
                method_exists($item, $method) and
                is_callable([$item, $method])
            ) {
                array_push($result, $item->{$method}());
            } else {
                array_push($result, $item->toArray());
            }
        }
        return $result;
    }
}

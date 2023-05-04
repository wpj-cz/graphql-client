<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\DataObjects\Product;

class RelatedItem
{
    public int $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }
}
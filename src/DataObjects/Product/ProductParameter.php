<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\DataObjects\Product;

final class ProductParameter
{
    public int $productId;
    public int $parameterId;
    public array $values;
    public bool $append;

    public function __construct(int $productId, int $parameterId, array $values, bool $append = false)
    {
        foreach ($values as $value) {
            if (!($value instanceof ParameterValue)) {
                throw new \InvalidArgumentException('Argument "$values" must be type of ParameterValue[]!');
            }
        }

        $this->productId = $productId;
        $this->parameterId = $parameterId;
        $this->values = $values;
        $this->append = $append;
    }
}

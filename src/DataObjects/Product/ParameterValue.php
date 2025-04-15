<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\DataObjects\Product;

use WpjShop\GraphQL\Enums\ParameterType;

final class ParameterValue
{
    private $value;
    private string $type;

    public function __construct($value, string $type = ParameterType::LIST)
    {
        $this->value = $value;
        $this->type = $type;
    }

    public function getValue()
    {
        if ($this->type === ParameterType::NUMBER) {
            return (float) $this->value;
        }

        return (string) $this->value;
    }

    public function getType(): string
    {
        return mb_strtolower($this->type);
    }
}

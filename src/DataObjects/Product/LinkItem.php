<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\DataObjects\Product;

class LinkItem
{
    private string $name;
    private string $link;
    private string $type;

    public function __construct(string $name, string $link, string $type = 'link')
    {
        $this->name = $name;
        $this->link = $link;
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getType(): string
    {
        return mb_strtolower($this->type);
    }
}

<?php

declare(strict_types=1);

namespace WpjShop\GraphQL;

use WpjShop\GraphQL\Services\Parameter;
use WpjShop\GraphQL\Services\Product;
use WpjShop\GraphQL\Services\Seller;

final class Client
{
    public Product $product;
    public Parameter $parameter;
    public Seller $seller;

    private \GraphQL\Client $client;

    public function __construct(string $endpoint, string $accessToken)
    {
        $this->client = new \GraphQL\Client(
            $endpoint,
            [
                'X-Access-Token' => $accessToken,
            ],
            [
                'timeout' => 60,
            ]
        );

        $this->createServices();
    }

    private function createServices(): void
    {
        foreach ($this->getServicesClasses() as $class) {
            $service = new $class($this->client);
            $this->{mb_strtolower((new \ReflectionClass($service))->getShortName())} = $service;
        }
    }

    private function getServicesClasses(): array
    {
        return [
            Product::class,
            Parameter::class,
            Seller::class,
        ];
    }
}
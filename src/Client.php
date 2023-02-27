<?php

declare(strict_types=1);

namespace WpjShop\GraphQL;

use GraphQL\Results;
use WpjShop\GraphQL\Services\Order;
use WpjShop\GraphQL\Services\Parameter;
use WpjShop\GraphQL\Services\Product;
use WpjShop\GraphQL\Services\Seller;
use WpjShop\GraphQL\Services\Store;

final class Client
{
    public Order $order;
    public Product $product;
    public Parameter $parameter;
    public Seller $seller;
    public Store $store;

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

    public function runQuery($query, bool $resultsAsArray = false, array $variables = []): Results
    {
        return $this->client->runQuery($query, $resultsAsArray, $variables);
    }

    public function runRawQuery(string $queryString, $resultsAsArray = false, array $variables = []): Results
    {
        return $this->client->runRawQuery($queryString, $resultsAsArray, $variables);
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
            Order::class,
            Product::class,
            Parameter::class,
            Seller::class,
            Store::class,
        ];
    }
}

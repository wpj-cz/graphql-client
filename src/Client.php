<?php

declare(strict_types=1);

namespace WpjShop\GraphQL;

use GraphQL\Results;
use WpjShop\GraphQL\Services\EditableContent;
use WpjShop\GraphQL\Services\Order;
use WpjShop\GraphQL\Services\Parameter;
use WpjShop\GraphQL\Services\Producer;
use WpjShop\GraphQL\Services\Product;
use WpjShop\GraphQL\Services\Section;
use WpjShop\GraphQL\Services\Seller;
use WpjShop\GraphQL\Services\Store;

class Client
{
    public Order $order;
    public Product $product;
    public Producer $producer;
    public Parameter $parameter;
    public Section $section;
    public Seller $seller;
    public Store $store;
    public EditableContent $editableContent;

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
                'allow_redirects' => ['strict' => true],
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

    protected function getServicesClasses(): array
    {
        return [
            Order::class,
            Product::class,
            Producer::class,
            Parameter::class,
            Section::class,
            Seller::class,
            Store::class,
            EditableContent::class,
        ];
    }

    private function createServices(): void
    {
        foreach ($this->getServicesClasses() as $class) {
            $service = new $class($this->client);
            $this->{lcfirst((new \ReflectionClass($service))->getShortName())} = $service;
        }
    }
}

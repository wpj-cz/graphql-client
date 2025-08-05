<?php

declare(strict_types=1);

namespace WpjShop\GraphQL;

use GraphQL\Results;
use WpjShop\GraphQL\Services\Changes;
use WpjShop\GraphQL\Services\Configuration;
use WpjShop\GraphQL\Services\EditableContent;
use WpjShop\GraphQL\Services\Order;
use WpjShop\GraphQL\Services\Parameter;
use WpjShop\GraphQL\Services\Producer;
use WpjShop\GraphQL\Services\Product;
use WpjShop\GraphQL\Services\Reclamation;
use WpjShop\GraphQL\Services\ReturnDto;
use WpjShop\GraphQL\Services\Section;
use WpjShop\GraphQL\Services\Seller;
use WpjShop\GraphQL\Services\Store;
use WpjShop\GraphQL\Services\Variation;

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
    public Changes $changes;
    public ReturnDto $return;
    public Configuration $configuration;
    public Variation $variation;
    public Reclamation $reclamation;

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
            Changes::class,
            'return' => ReturnDto::class,
            Configuration::class,
            Variation::class,
            Reclamation::class,
        ];
    }

    private function createServices(): void
    {
        foreach ($this->getServicesClasses() as $key => $class) {
            $service = new $class($this->client);
            if (is_numeric($key)) {
                $propertyName = lcfirst((new \ReflectionClass($service))->getShortName());
            } else {
                $propertyName = $key;
            }

            $this->{$propertyName} = $service;
        }
    }
}

<?php

declare(strict_types=1);

namespace WpjShop\GraphQL;

use GraphQL\Results;
use WpjShop\GraphQL\Services\Changes;
use WpjShop\GraphQL\Services\Charge;
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
use WpjShop\GraphQL\Services\ServiceInterface;
use WpjShop\GraphQL\Services\Store;
use WpjShop\GraphQL\Services\Variation;
use WpjShop\GraphQL\Services\VariationLabel;

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
    public VariationLabel $variationLabel;
    public Reclamation $reclamation;
    public Charge $charge;

    private \GraphQL\Client $client;

    private array $defaultHttpOptions = [
        'timeout' => 60,
        'allow_redirects' => ['strict' => true],
    ];

    public function __construct(string $endpoint, string $accessToken, array $httpOptions = [])
    {
        $this->client = new \GraphQL\Client(
            $endpoint,
            [
                'X-Access-Token' => $accessToken,
            ],
            array_merge($this->defaultHttpOptions, $httpOptions),
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
        $refClass = new \ReflectionClass($this);

        foreach ($refClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $class = $property->getType()->getName();
            if (!is_a($class, ServiceInterface::class, true)) {
                continue;
            }

            $this->{$property->getName()} = new $class($this->client);
        }
    }
}

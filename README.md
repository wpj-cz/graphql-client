# WpjShop GraphQL Client

## Installation
```
composer require wpjshop/graphql-client
```

## Documentation

GraphQL API documentation [here](https://graphql-docs.wpjshop.cz/)

### Instantiate a client
```php
<?php
$client = new \WpjShop\GraphQL\Client('https://your-domain/admin/graphql', '<authentication-token>');
```

### Using the GraphQL Client
Simple use:
```php
<?php
// Returns an array with product data returned by the GraphQL server.
$result = $client->product->get(1);
```

Usage with custom field selection:
```php
<?php
// Returns an array with data defined by `setSelection` method
$result = $client->product
    ->setSelection(
        [
            'id',
            'variations' => [
                'id',
                'price' => [
                    'withVat',
                    'vat',
                    'currency' => [
                        'code',
                    ],
                ],
            ],
        ]
)->get(1);
```

## GraphQL Client Reference

TODO
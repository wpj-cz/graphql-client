<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;

class Order extends AbstractService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'language' => [
                'code',
            ],
            'code',
            'email',
            'dateCreated',
            'status',
            'totalPrice' => [
                'withVat',
                'withoutVat',
                'currency' => [
                    'code',
                ],
            ],
            'items' => [
                'id',
                'type',
                'pieces',
                'name',
                'totalPrice' => [
                    'withVat',
                    'withoutVat',
                    'vat',
                    'currency' => [
                        'code',
                    ],
                ],
            ],
        ];
    }

    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('order')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    public function list(int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('orders', true)
            ->setArguments(['offset' => $offset, 'limit' => $limit]);

        return $this->executeQuery($gql);
    }

    public function update(int $id, array $data): array
    {
        $gql = (new Mutation('orderUpdate'))
             ->setVariables([new Variable('order', 'OrderUpdateInput', true)])
             ->setArguments(['input' => '$order'])
             ->setSelectionSet(
                 [
                     'result',
                     (new Query('order'))
                         ->setSelectionSet(
                             $this->getSelectionSet()
                         ),
                 ]
             );

        return $this->executeQuery(
            $gql,
            ['order' => array_merge(['id' => $id], $data)]
        );
    }

    public function create(array $data): array
    {
        throw new \RuntimeException('Order create not implemented');
    }

    /**
     * Returns orders collection with orders from specified date to now.
     */
    public function listFromDate(\DateTime $from, int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('orders', true)
            ->setVariables([
                new Variable('filter', 'OrderFilterInput'),
                new Variable('sort', 'OrderSortInput'),
            ])
            ->setArguments([
                'offset' => $offset,
                'limit' => $limit,
                'filter' => '$filter',
                'sort' => '$sort',
            ]);

        return $this->executeQuery($gql, [
            'filter' => ['dateFrom' => $from->format('Y-m-d H:i:s')],
            'sort' => ['dateCreated' => 'ASC'],
        ]);
    }
}

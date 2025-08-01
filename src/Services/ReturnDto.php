<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;

class ReturnDto extends AbstractEntityService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'language' => [
                'name',
            ],
            'currency' => [
                'code',
            ],
            'code',
            'dateCreated',
            'dateAccepted',
            'dateHandle',
            'email',
            'bankAccount',
            'status' => [
                'id',
                'name',
            ],
            'totalPrice' => [
                'withVat',
                'withoutVat',
            ],
            'items' => [
                'id',
                'productId',
                'orderItemId',
                'name',
                'variationId',
                'pieces',
                'piecePrice' => [
                    'withVat',
                    'withoutVat',
                ],
                'totalPrice' => [
                    'withVat',
                    'withoutVat',
                ],
                'returnReason' => [
                    'id',
                    'name',
                ],
            ],
        ];
    }

    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('return')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('returns', true);

        $arguments = ['offset' => $offset, 'limit' => $limit];
        $variables = [];

        if ($filter) {
            $arguments['filter'] = '$filter';
            $variables[] = new Variable('filter', 'ReturnFilterInput', true);
        }

        if ($sort) {
            $arguments['sort'] = '$sort';
            $variables[] = new Variable('sort', 'ReturnSortInput', true);
        }

        $gql->setVariables($variables)
            ->setArguments($arguments);

        return $this->executeQuery($gql, ['filter' => $filter, 'sort' => $sort]);
    }

    public function update(int $id, array $data): array
    {
        $gql = (new Mutation('returnUpdate'))
            ->setVariables([new Variable('return', 'ReturnUpdateInput', true)])
            ->setArguments(['input' => '$return'])
            ->setSelectionSet(
                [
                    'result',
                    (new Query('return'))
                        ->setSelectionSet(
                            $this->getSelectionSet()
                        ),
                ]
            );

        return $this->executeQuery(
            $gql,
            ['return' => array_merge(['id' => $id], $data)]
        );
    }

    public function create(array $data): array
    {
        $gql = (new Mutation('returnCreate'))
            ->setVariables([new Variable('return', 'ReturnCreateInput', true)])
            ->setArguments(['input' => '$return'])
            ->setSelectionSet(
                $this->getResponseSelectionSet()
            );

        return $this->executeQuery(
            $gql,
            ['return' => $data]
        );
    }

    private function getResponseSelectionSet(): array
    {
        return [
            'result',
            (new Query('return'))
                ->setSelectionSet(
                    $this->getSelectionSet()
                ),
        ];
    }
}

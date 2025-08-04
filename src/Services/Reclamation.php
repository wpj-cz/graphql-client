<?php
declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;

class Reclamation extends AbstractEntityService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'language' => [
                'name',
            ],
            'currency' => [
                'code'
            ],
            'code',
            'bankAccount',
            'dateCreated',
            'dateAccepted',
            'dateHandle',
            'email',
            'noteUser',
            'conclusion',
            'handleType' => [
                'handleType',
                'handleTypeName'
            ],
            'preferredHandleType'=> [
                'handleType',
                'handleTypeName'
            ],
            'item' => [
                'id',
                'orderId',
                'pieces',
                'name',
                'vat',
                'date',
                'piecePrice' => [
                    'withoutVat',
                ],
                'totalPrice' => [
                    'withoutVat'
                ],
                'discount'
            ]
        ];
    }

    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('reclamation')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('reclamations', true);

        $arguments = ['offset' => $offset, 'limit' => $limit];
        $variables = [];

        if ($filter) {
            $arguments['filter'] = '$filter';
            $variables[] = new Variable('filter', 'ReclamationFilterInput', true);
        }

        if ($sort) {
            $arguments['sort'] = '$sort';
            $arguments[] = new Variable('sort', 'ReclamationSortInput', true);
        }

        $gql->setVariables($variables)
            ->setArguments($arguments);

        return $this->executeQuery($gql, ['filter' => $filter, 'sort' => $sort]);
    }

    public function update(int $id, array $data): array
    {
        $gql = (new Mutation('reclamationUpdate'))
            ->setVariables([new Variable('reclamation', 'ReclamationUpdateInput', true)])
            ->setArguments(['input' => '$reclamation'])
            ->setSelectionSet(
                [
                    'result',
                    (new Query('reclamation'))
                        ->setSelectionSet(
                            $this->getSelectionSet()
                        ),
                ]
            );

        return $this->executeQuery(
            $gql,
            ['reclamation' => array_merge(['id' => $id], $data)]
        );
    }

    public function create(array $data): array
    {
        $gql = (new Mutation('reclamationCreate'))
            ->setVariables([new Variable('reclamation', 'ReclamationCreateInput', true)])
            ->setArguments(['input' => '$reclamation'])
            ->setSelectionSet(
                [
                    'result',
                    (new Query('reclamation'))
                        ->setSelectionSet(
                            $this->getSelectionSet()
                        ),
                ]
            );

        return $this->executeQuery($gql, ['reclamation' => $data]);
    }
}
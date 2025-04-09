<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Variable;
use WpjShop\GraphQL\Exception\MethodNotImplementedException;

class Store extends AbstractEntityService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'visible',
            'name',
            'type',
        ];
    }

    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('store')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('stores', true);

        return $this->executeQuery($gql);
    }

    public function update(int $id, array $data): array
    {
        throw new MethodNotImplementedException('Store update not implemented!');
    }

    public function create(array $data): array
    {
        $gql = (new Mutation('storeCreate'))
            ->setVariables([new Variable('input', 'StoreCreateInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                $this->getSelectionSet()
            );

        return $this->executeQuery($gql, [
            'input' => $data,
        ]);
    }
}

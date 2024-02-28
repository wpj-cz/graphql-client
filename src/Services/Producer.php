<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use WpjShop\GraphQL\Exception\MethodNotImplementedException;

class Producer extends AbstractService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'name',
        ];
    }

    /**
     * Returns producer by ID.
     */
    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('producer')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    /**
     * Returns producers list.
     */
    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('producers', true);

        return $this->executeQuery($gql);
    }

    public function update(int $id, array $data): array
    {
        throw new MethodNotImplementedException('Producer update is not implemented!');
    }

    public function create(array $data): array
    {
        throw new MethodNotImplementedException('Producer create is not implemented!');
    }
}

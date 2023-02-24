<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Variable;

/**
 * Service that works with parameters.
 */
class Parameter extends AbstractService
{
    /**
     * Returns parameter by ID.
     */
    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('parameter')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    /**
     * Returns parameters list.
     */
    public function list(int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('parameters', true);

        return $this->executeQuery($gql);
    }

    public function update(int $id, array $data): array
    {
        throw new \RuntimeException('Parameter update not implemented!');
    }

    /**
     * Create new parameter.
     */
    public function create(array $data): array
    {
        $gql = (new Mutation('parameterCreate'))
            ->setVariables([new Variable('input', 'ParameterCreateInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                $this->getSelectionSet()
            );

        return $this->executeQuery($gql, [
            'input' => $data,
        ]);
    }

    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'name',
            'type',
            'unit',
        ];
    }
}

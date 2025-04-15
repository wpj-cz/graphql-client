<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Variable;
use WpjShop\GraphQL\Exception\MethodNotImplementedException;

/**
 * Service that works with parameters.
 */
class Parameter extends AbstractEntityService
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
    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('parameters', true);

        return $this->executeQuery($gql);
    }

    public function update(int $id, array $data): array
    {
        throw new MethodNotImplementedException('Parameter update not implemented!');
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

    /**
     * Translate parameter.
     */
    public function translate(int $id, string $language, array $data): array
    {
        $gql = (new Mutation('parameterTranslate'))
            ->setVariables([new Variable('input', 'ParameterTranslationInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                ['result']
            );

        return $this->executeQuery($gql, [
            'input' => array_merge(['parameterId' => $id, 'language' => $language], $data),
        ]);
    }

    /**
     * Returns list of parameters values.
     *
     * @param int|int[]|null $parameterId
     */
    public function getParameterValues(array|int|null $parameterId = null, int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('parameterValues', true)
            ->setSelectionSet(
                $this->createSelectionSet($this->getParameterValuesSelectionSet())
            )
            ->setVariables([
                new Variable('offset', 'Int', true),
                new Variable('limit', 'Int', true),
                new Variable('filter', 'ParameterListFilterInput'),
            ])
            ->setArguments([
                'offset' => '$offset',
                'limit' => '$limit',
                'filter' => '$filter',
            ]);

        if ($parameterId !== null && !is_array($parameterId)) {
            $parameterId = [$parameterId];
        }

        return $this->executeQuery($gql, [
            'offset' => $offset,
            'limit' => $limit,
            'filter' => ['parameterId' => $parameterId],
        ]
        );
    }

    public function translateParameterValue(int $id, string $language, array $data): array
    {
        $gql = (new Mutation('parameterValuesTranslate'))
            ->setVariables([new Variable('input', 'ParameterValuesTranslationInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                ['result']
            );

        return $this->executeQuery($gql, [
            'input' => array_merge(['valueId' => $id, 'language' => $language], $data),
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

    protected function getParameterValuesSelectionSet(): array
    {
        return [
            'items' => [
                'id',
                'value',
                'description',
            ],
            'hasNextPage',
            'hasPreviousPage',
        ];
    }
}

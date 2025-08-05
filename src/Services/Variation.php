<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;
use WpjShop\GraphQL\Exception\MethodNotImplementedException;

class Variation extends AbstractEntityService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'code',
            'inStore',
            'ean',
            'title',
            'labels' => [
                'label' => [
                    'id',
                    'name',
                ],
                'value',
                'title',
            ],
            'values' => [
                'label' => [
                    'id',
                    'name',
                ],
                'value',
                'title',
            ],
            'visible',
            'price' => [
                'withVat',
                'withoutVat',
            ],
            'deliveryTime' => [
                'id',
                'name',
            ],
        ];
    }

    public function get(int $id): ?array
    {
        throw new MethodNotImplementedException('Variation get not implemented');
    }

    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        throw new MethodNotImplementedException('Variation list not implemented');
    }

    public function create(array $data): array
    {
        return $this->executeQuery(
            $this->prepareVariationCreateOrUpdateMutation(),
            ['variation' => $data]
        );
    }

    public function update(int $id, array $data): array
    {
        return $this->executeQuery(
            $this->prepareVariationCreateOrUpdateMutation(),
            ['variation' => array_merge(['id' => $id], $data)]
        );
    }

    private function prepareVariationCreateOrUpdateMutation(): Mutation
    {
        return (new Mutation('variationCreateOrUpdate'))
            ->setVariables([new Variable('variation', 'VariationModifyInput', true)])
            ->setArguments(['variation' => '$variation'])
            ->setSelectionSet(
                $this->getResponseSelectionSet()
            );
    }

    private function getResponseSelectionSet(): array
    {
        return [
            'result',
            (new Query('variation'))
                ->setSelectionSet(
                    $this->getSelectionSet()
                )
        ];
    }
}

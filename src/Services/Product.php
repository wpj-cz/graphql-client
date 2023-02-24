<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;
use WpjShop\GraphQL\DataObjects\Product\ProductParameter;

/**
 * Service that works with product.
 */
class Product extends AbstractService
{
    /**
     * Returns product by ID.
     */
    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('product')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    /**
     * Returns products list.
     */
    public function list(int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('products', true)
            ->setArguments(['offset' => $offset, 'limit' => $limit]);

        return $this->executeQuery($gql);
    }

    /**
     * Updates product by ID.
     */
    public function update(int $id, array $data): array
    {
        return $this->executeQuery(
            $this->getProductCreateOrUpdateMutation(),
            ['product' => array_merge(['id' => $id], $data)]
        );
    }

    /**
     * Create new product.
     */
    public function create(array $data): array
    {
        return $this->executeQuery(
            $this->getProductCreateOrUpdateMutation(),
            ['product' => $data]
        );
    }

    /**
     * Update product parameter values.
     *
     * @param ProductParameter[] $values
     */
    public function updateParameter(int $productId, int $parameterId, array $values, bool $append = false): array
    {
        $gql = (new Mutation('productParameterUpdate'))
            ->setVariables([new Variable('input', 'ProductParameterInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                $this->getProductMutateResponseSelectionSet()
            );

        $valuesInput = [];
        foreach ($values as $item) {
            $valuesInput[][$item->getType()] = $item->getValue();
        }

        return $this->executeQuery(
            $gql,
            [
                'input' => [
                    'productId' => $productId,
                    'parameterId' => $parameterId,
                    'values' => $valuesInput,
                    'append' => $append,
                ],
            ]
        );
    }

    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'code',
            'title',
            'discount',
            'price' => [
                'withVat',
                'vat',
                'currency' => [
                    'code',
                ],
            ],
        ];
    }

    private function getProductCreateOrUpdateMutation(): Mutation
    {
        return (new Mutation('productCreateOrUpdate'))
            ->setVariables([new Variable('product', 'ProductModifyInput', true)])
            ->setArguments(['product' => '$product'])
            ->setSelectionSet(
                $this->getProductMutateResponseSelectionSet()
            );
    }

    private function getProductMutateResponseSelectionSet(): array
    {
        return [
            'result',
            (new Query('product'))
                ->setSelectionSet(
                    $this->getSelectionSet()
                ),
        ];
    }
}

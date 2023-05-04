<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;
use WpjShop\GraphQL\DataObjects\Product\CollectionItem;
use WpjShop\GraphQL\DataObjects\Product\LinkItem;
use WpjShop\GraphQL\DataObjects\Product\ProductParameter;
use WpjShop\GraphQL\DataObjects\Product\RelatedItem;

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

    public function getByCode(string $code): ?array
    {
        $gql = $this->createBaseQuery('products', true)
            ->setVariables([new Variable('filter', 'ProductFilterInput', true)])
            ->setArguments(['filter' => '$filter']);

        return $this->executeQuery($gql, ['filter' => ['code' => $code]])['items'][0] ?? null;
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

    /**
     * Update product links.
     *
     * @param LinkItem[] $links
     */
    public function updateLinks(int $productId, array $links, bool $append = false): array
    {
        $gql = (new Mutation('productLinkUpdate'))
            ->setVariables([new Variable('input', 'ProductLinkInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                $this->getProductMutateResponseSelectionSet()
            );

        $linksInput = [];

        foreach ($links as $link) {
            $linksInput[] = [
                'name' => $link->getName(),
                'link' => $link->getLink(),
                'type' => $link->getType(),
            ];
        }

        return $this->executeQuery(
            $gql,
            [
                'input' => [
                    'productId' => $productId,
                    'links' => $linksInput,
                    'append' => $append,
                ],
            ]
        );
    }

    /**
     * Update product collection.
     *
     * @param CollectionItem[] $products
     */
    public function updateCollections(int $productId, array $products, bool $append = false): array
    {
        $gql = (new Mutation('productCollectionUpdate'))
            ->setVariables([new Variable('input', 'CollectionProductsInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                $this->getProductMutateResponseSelectionSet()
            );

        $productsInput = [];

        foreach ($products as $product) {
            $productsInput[] = [
                'productId' => $product->productId,
            ];
        }

        return $this->executeQuery(
            $gql,
            [
                'input' => [
                    'productId' => $productId,
                    'products' => $productsInput,
                    'append' => $append,
                ],
            ]
        );
    }

    /**
     * Update product related.
     *
     * @param RelatedItem[] $products
     */
    public function updateRelated(int $productId, array $products, bool $append = false): array
    {
        $gql = (new Mutation('productRelatedUpdate'))
            ->setVariables([new Variable('input', 'RelatedProductsInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                $this->getProductMutateResponseSelectionSet()
            );

        $productsInput = [];

        foreach ($products as $product) {
            $productsInput[] = [
                'productId' => $product->productId,
            ];
        }

        return $this->executeQuery(
            $gql,
            [
                'input' => [
                    'productId' => $productId,
                    'products' => $productsInput,
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

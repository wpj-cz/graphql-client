<?php

declare(strict_types=1);

namespace Wpj\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;

/**
 * Service that works with sellers.
 */
class Seller extends AbstractService
{
    /**
     * Returns seller by ID.
     */
    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('seller')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    /**
     * Returns sellers list.
     */
    public function list(int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('sellers', true);

        return $this->executeQuery($gql);
    }

    /**
     * Updates seller.
     */
    public function update(int $id, array $data): array
    {
        return $this->executeQuery(
            $this->getSellerCreateOrUpdateMutation(),
            ['seller' => array_merge(['id' => $id], $data)]
        );
    }

    /**
     * Create new seller.
     */
    public function create(array $data): array
    {
        return $this->executeQuery(
            $this->getSellerCreateOrUpdateMutation(),
            ['seller' => $data]
        );
    }

    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'visible',
            'name',
        ];
    }

     private function getSellerCreateOrUpdateMutation(): Mutation
    {
        return (new Mutation('sellerCreateOrUpdate'))
            ->setVariables([new Variable('seller', 'SellerInput', true)])
            ->setArguments(['input' => '$seller'])
            ->setSelectionSet(
                [
                    'result',
                    (new Query('seller'))
                        ->setSelectionSet(
                            $this->getSelectionSet()
                        )
                ]
            );
    }
}
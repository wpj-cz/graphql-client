<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Variable;
use WpjShop\GraphQL\Exception\MethodNotImplementedException;

class Charge extends AbstractEntityService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'currency' => [
                'code',
            ],
            'name',
            'nameAdmin',
            'active',
            'price' => [
                'withVat',
                'withoutVat',
                'currency' => [
                    'code',
                ],
            ],
            'description',
            'description',
            'included',
            'required',
            'checked',
            'percentage',
            'oneTime',
            'productId',
        ];
    }

    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('charge')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('charges', true);

        $arguments = ['offset' => $offset, 'limit' => $limit];
        $variables = [];

        if ($filter) {
            $arguments['filter'] = '$filter';
            $variables[] = new Variable('filter', 'ChargeFilterInput', true);
        }

        if ($sort) {
            $arguments['sort'] = '$sort';
            $variables[] = new Variable('sort', 'ChargeSortInput', true);
        }

        $gql->setVariables($variables)
            ->setArguments($arguments);

        return $this->executeQuery($gql, ['filter' => $filter, 'sort' => $sort]);
    }

    public function update(int $id, array $data): array
    {
        throw new MethodNotImplementedException('Charge update not implemented');
    }

    public function create(array $data): array
    {
        throw new MethodNotImplementedException('Charge create not implemented');
    }
}

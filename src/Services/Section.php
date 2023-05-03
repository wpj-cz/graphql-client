<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Query;
use WpjShop\GraphQL\Exception\MethodNotImplementedException;

class Section extends AbstractService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'name',
            'visible',
            'url',
            'isVirtual',
            (new Query('parent'))
                ->setSelectionSet(
                    [
                        'id',
                        'name',
                    ]
                ),
        ];
    }

    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('section')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    public function list(int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('sectionsList', true);

        return $this->executeQuery($gql);
    }

    public function update(int $id, array $data): array
    {
        throw new MethodNotImplementedException('Section update is not implemented');
    }

    public function create(array $data): array
    {
        throw new MethodNotImplementedException('Section create is not implemented');
    }
}

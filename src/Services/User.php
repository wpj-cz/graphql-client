<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;

/**
 * Service that works with users.
 */
class User extends AbstractEntityService
{
    /**
     * Returns user by ID.
     */
    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('user')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    /**
     * Returns users list.
     */
    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('users', true);

        $arguments = ['offset' => $offset, 'limit' => $limit];
        $variables = [];

        if ($filter) {
            $arguments['filter'] = '$filter';
            $variables[] = new Variable('filter', 'UserFilterInput', true);
        }

        if ($sort) {
            $arguments['sort'] = '$sort';
            $variables[] = new Variable('sort', 'UserSortInput', true);
        }

        $gql->setVariables($variables)
            ->setArguments($arguments);

        return $this->executeQuery($gql, ['filter' => $filter, 'sort' => $sort]);
    }

    /**
     * Updates user.
     */
    public function update(int $id, array $data): array
    {
        return $this->executeQuery(
            $this->getUserCreateOrUpdateMutation(),
            ['user' => array_merge(['id' => $id], $data)]
        );
    }

    /**
     * Create new user.
     */
    public function create(array $data): array
    {
        return $this->executeQuery(
            $this->getUserCreateOrUpdateMutation(),
            ['user' => $data]
        );
    }

    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'user',
            'email',
            'name',
            'surname',
            'userName',
        ];
    }

    private function getUserCreateOrUpdateMutation(): Mutation
    {
        return (new Mutation('userCreateOrUpdate'))
            ->setVariables([new Variable('user', 'UserInput', true)])
            ->setArguments(['input' => '$user'])
            ->setSelectionSet(
                [
                    'result',
                    (new Query('user'))
                        ->setSelectionSet(
                            $this->getSelectionSet()
                        ),
                ]
            );
    }
}

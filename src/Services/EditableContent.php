<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Variable;
use WpjShop\GraphQL\Exception\MethodNotImplementedException;

class EditableContent extends AbstractService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'areas' => [
                'id',
                'name',
                'content',
                'data',
            ],
        ];
    }

    public function get(int $id): ?array
    {
        throw new MethodNotImplementedException();
    }

    public function list(int $offset = 0, int $limit = 100): array
    {
        throw new MethodNotImplementedException();
    }

    public function create(array $data): array
    {
        throw new MethodNotImplementedException();
    }

    public function update(int $id, array $data): array
    {
        $gql =  (new Mutation('editableContentUpdate'))
             ->setVariables([new Variable('input', 'EditableContentInput', true)])
             ->setArguments(['input' => '$input'])
             ->setSelectionSet(
                 $this->createSelectionSet(
                     [
                         'result',
                         'editableContent' => $this->getDefaultSelectionSet(),
                     ]
                 )
             );

        return $this->executeQuery(
            $gql,
            ['input' => array_merge(['id' => $id], $data)]
        );
    }

    public function translate(int $id, string $language, array $data): array
    {
        $gql =  (new Mutation('editableContentTranslate'))
             ->setVariables([new Variable('input', 'EditableContentTranslationInput', true)])
             ->setArguments(['input' => '$input'])
             ->setSelectionSet(
                 [
                     'result',
                 ]
             );

        return $this->executeQuery(
            $gql,
            ['input' => array_merge(['id' => $id, 'language' => $language], $data)]
        );
    }
}
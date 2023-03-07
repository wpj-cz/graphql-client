<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Query;

abstract class AbstractService implements ServiceInterface
{
    protected Client $client;

    protected array $selection = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setSelection(array $selection): self
    {
        $this->selection = $this->recursivelyCreateSelectionSet($selection);

        return $this;
    }

    abstract protected function getDefaultSelectionSet(): array;

    protected function getSelectionSet(): array
    {
        return empty($this->selection) ?
            $this->recursivelyCreateSelectionSet($this->getDefaultSelectionSet()) : $this->selection;
    }

    protected function executeQuery(Query $gql, array $variables = [], bool $resetResult = true): ?array
    {
        try {
            $result = $this->client
                ->runQuery($gql, true, $variables)
                ->getData();

            if ($resetResult && is_array($result)) {
                return reset($result);
            }

            return $result;
        } catch (QueryError $e) {
            // handle not found error and return null as result
            if (($e->getErrorDetails()['extensions']['category'] ?? null) === 'NOT_FOUND') {
                return null;
            }

            // other errors should be thrown
            throw $e;
        }
    }

    protected function createBaseQuery(string $queryName, bool $list = false): Query
    {
        $selectionSet = $this->getSelectionSet();
        if ($list) {
            $selectionSet = [
                (new Query('items'))
                    ->setSelectionSet($this->getSelectionSet()),
                'hasNextPage',
                'hasPreviousPage',
            ];
        }

        return (new Query($queryName))
            ->setSelectionSet($selectionSet);
    }

    private function recursivelyCreateSelectionSet(array $selection): array
    {
        $selectionSet = [];

        foreach ($selection as $key => $item) {
            if (is_array($item)) {
                $selectionSet[] = (new Query($key))
                    ->setSelectionSet($this->recursivelyCreateSelectionSet($item));

                continue;
            }

            $selectionSet[] = $item;
        }

        return $selectionSet;
    }
}

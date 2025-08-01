<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\Mutation;
use GraphQL\Query;

class AbstractService implements ServiceInterface
{
    public function __construct(protected Client $client)
    {
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
            // mutation should always throw 404 errors
            if (!($gql instanceof Mutation)) {
                // handle not found error and return null as result
                if (($e->getErrorDetails()['extensions']['category'] ?? null) === 'NOT_FOUND') {
                    return null;
                }
            }

            // other errors should be thrown
            throw $e;
        }
    }

    protected function executeRawQuery(string $gql, array $variables = [], bool $resetResult = true)
    {
        try {
            $result = $this->client
                ->runRawQuery($gql, true, $variables)
                ->getData();

            if ($resetResult && is_array($result)) {
                return reset($result);
            }

            return $result;
        } catch (QueryError $e) {
            // mutation should always throw 404 errors
            if (!($gql instanceof Mutation)) {
                // handle not found error and return null as result
                if (($e->getErrorDetails()['extensions']['category'] ?? null) === 'NOT_FOUND') {
                    return null;
                }
            }

            // other errors should be thrown
            throw $e;
        }
    }

    protected function createSelectionSet(array $selection): array
    {
        return $this->recursivelyCreateSelectionSet($selection);
    }

    protected function recursivelyCreateSelectionSet(array $selection): array
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

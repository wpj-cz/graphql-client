<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Variable;
use WpjShop\GraphQL\Exception\ChangesException;

class Changes extends AbstractService
{
    private const FETCH_LIMIT_DEFAULT = 100;

    /**
     * @param callable(array): bool $fn
     * @param int $limit The maximum number of items to process in a single execution. Processing stops once this limit is reached, even if more items are available.
     *
     * @throws ChangesException
     */
    public function consume(callable $fn, int $limit = 1000): void
    {
        $processedCount = 0;

        while ($processedCount < $limit && ($batch = $this->fetch(min(self::FETCH_LIMIT_DEFAULT, $limit - $processedCount)))) {
            $processedCount += count($batch);

            // call callable so changes are processed
            if (!$fn($batch)) {
                continue;
            }

            $confirmId = max(array_map(fn (array $x) => $x['metadata']['id'], $batch));

            // callable returned true, so batch  was successfully processed and we can confirm it
            if (!$this->confirm($confirmId)) {
                throw new ChangesException('Confirmation of processed changes was not successful.');
            }
        }
    }

    public function fetch(int $limit = self::FETCH_LIMIT_DEFAULT): array
    {
        return $this->executeRawQuery(
            $this->getChangesGQL(),
            ['limit' => $limit],
        );
    }

    public function confirm(int $changeId): bool
    {
        $gql = (new Mutation('changesConfirm'))
            ->setVariables([new Variable('changeId', 'Int', true)])
            ->setArguments(['changeId' => '$changeId'])
            ->setSelectionSet(['result']);

        return $this->executeQuery(
            $gql,
            ['changeId' => $changeId]
        )['result'] ?? false;
    }

    public function reset(\DateTimeInterface $to): bool
    {
        $gql = (new Mutation('changesReset'))
            ->setVariables([new Variable('date', 'DateTime', true)])
            ->setArguments(['date' => '$date'])
            ->setSelectionSet(['result']);

        return $this->executeQuery(
            $gql,
            ['date' => $to->format(\DateTimeInterface::ATOM)]
        )['result'] ?? false;
    }

    protected function getChangesGQL(): string
    {
        return <<<GQL
            query (\$limit: Int) {
              changes (limit: \$limit) {
                type: __typename
                ... on ChangeInterface {
                  metadata {
                    id
                    action
                    date
                  }
                }
                ... on ProductChange {
                  productId
                  changedFields
                }
                ... on ProductVariationChange {
                  productId
                  variationId
                  changedFields
                }
                ... on ProductQuantityChange {
                  productId
                  variationId
                  storeId
                  quantity
                  quantityPrevious
                }
                ... on ProductPriceChange {
                  productId
                  variationId
                  priceListId
                  price
                  pricePrevious
                  discount
                  discountPrevious
                }
                ... on OrderChange {
                  orderId
                  changedFields
                }
                ... on OrderItemChange {
                  orderId
                  orderItemId
                  changedFields
                }                
              }
            }
        GQL;
    }
}

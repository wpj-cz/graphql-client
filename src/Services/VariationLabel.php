<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Mutation;
use GraphQL\Query;
use GraphQL\Variable;
use KupShop\GraphQLBundle\ApiAdmin\Types\Catalog\Product\Input\VariationLabelFilterInput;

class VariationLabel extends AbstractEntityService
{
    protected function getDefaultSelectionSet(): array
    {
        return [
            'id',
            'name',
        ];
    }

    /**
     * Returns variation label by ID.
     */
    public function get(int $id): ?array
    {
        $gql = $this->createBaseQuery('variationLabel')
            ->setArguments(['id' => $id]);

        return $this->executeQuery($gql);
    }

    /**
     * Returns list of variation labels.
     */
    public function list(int $offset = 0, int $limit = 100, array $filter = [], array $sort = []): array
    {
        $gql = $this->createBaseQuery('variationLabels', true);

        $arguments = ['offset' => $offset, 'limit' => $limit];
        $variables = [];

        if ($filter) {
            $arguments['filter'] = '$filter';
            $variables[] = new Variable('filter', 'VariationLabelFilterInput');
        }

        $gql->setVariables($variables)
            ->setArguments($arguments);

        return $this->executeQuery($gql, ['filter' => $filter]);
    }

    /**
     * Updates variation label.
     */
    public function update(int $id, array $data): array
    {
        return $this->executeQuery(
            $this->getVariationLabelCreateOrUpdateMutation(),
            ['label' => array_merge(['id' => $id], $data)]
        );
    }

    /**
     * Create new variation label.
     */
    public function create(array $data): array
    {
        return $this->executeQuery(
            $this->getVariationLabelCreateOrUpdateMutation(),
            ['label' => $data]
        );
    }

    /**
     * Translate variation label.
     */
    public function translate(int $id, string $language, array $data): array
    {
        $gql = (new Mutation('translateVariationLabel'))
            ->setVariables([new Variable('input', 'VariationLabelTranslationInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                ['result']
            );

        return $this->executeQuery($gql, [
            'input' => array_merge(['variationLabelId' => $id, 'language' => $language], $data),
        ]);
    }

    /**
     * Returns list of variation labels values.
     *
     * @param int|int[]|null $variationLabelId
     */
    public function getVariationLabelValues(int|array|null $variationLabelId = null, int $offset = 0, int $limit = 100): array
    {
        $gql = $this->createBaseQuery('variationLabelValues', true)
            ->setSelectionSet(
                $this->createSelectionSet($this->getVariationLabelValuesSelectionSet())
            )
            ->setVariables([
                new Variable('offset', 'Int', true),
                new Variable('limit', 'Int', true),
                new Variable('filter', 'VariationValueFilterInput'),
            ])
            ->setArguments([
                'offset' => '$offset',
                'limit' => '$limit',
                'filter' => '$filter',
            ]);

        if ($variationLabelId !== null && !is_array($variationLabelId)) {
            $variationLabelId = [$variationLabelId];
        }

        return $this->executeQuery($gql, [
                'offset' => $offset,
                'limit' => $limit,
                'filter' => ['labelId' => $variationLabelId],
            ]
        );
    }

    /**
     * Update variation label value translation.
     */
    public function translateVariationLabelValue(int $id, string $language, array $data): array
    {
        $gql = (new Mutation('translateVariationLabelValue'))
            ->setVariables([new Variable('input', 'VariationLabelValueTranslationInput', true)])
            ->setArguments(['input' => '$input'])
            ->setSelectionSet(
                ['result']
            );

        return $this->executeQuery($gql, [
            'input' => array_merge(['variationValueId' => $id, 'language' => $language], $data),
        ]);
    }

    private function getVariationLabelCreateOrUpdateMutation(): Mutation
    {
        return (new Mutation('variationLabelCreateOrUpdate'))
            ->setVariables([new Variable('label', 'VariationLabelInput', true)])
            ->setArguments(['input' => '$label'])
            ->setSelectionSet(
                [
                    'result',
                    (new Query('label'))
                        ->setSelectionSet(
                            $this->getSelectionSet()
                        ),
                ]
            );
    }

    protected function getVariationLabelValuesSelectionSet(): array
    {
        return [
            'items' => [
                'id',
                'label' => [
                    'id',
                    'name',
                ],
                'value',
            ],
            'hasNextPage',
            'hasPreviousPage',
        ];
    }
}
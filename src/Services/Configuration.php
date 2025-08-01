<?php

declare(strict_types=1);

namespace WpjShop\GraphQL\Services;

use GraphQL\Query;

class Configuration extends AbstractService
{
    public function getOrderStatuses(): array
    {
        return $this->getConfiguration([
            'orderStatuses' => [
                'id',
                'name',
            ],
        ]);
    }

    public function getOrderSources(): array
    {
        return $this->getConfiguration([
            'orderSources' => [
                'source',
                'name',
            ],
        ]);
    }

    public function getDeliveryTypes(): array
    {
        return $this->getConfiguration([
            'deliveryTypes' => [
                'type',
                'name',
            ],
        ]);
    }

    public function getPaymentTypes(): array
    {
        return $this->getConfiguration([
            'paymentTypes' => [
                'type',
                'name',
            ],
        ]);
    }

    public function getDeliveryTimes(): array
    {
        return $this->getConfiguration([
            'deliveryTimes' => [
                'id',
                'name',
            ],
        ]);
    }

    public function getDropshipmentTypes(): array
    {
        return $this->getConfiguration([
            'dropshipmentTypes',
        ]);
    }

    public function getReturnStatuses(): array
    {
        return $this->getConfiguration([
            'returnStatuses' => [
                'id',
                'name',
            ],
        ]);
    }

    public function getReturnReasons(): array
    {
        return $this->getConfiguration([
            'returnReasons' => [
                'id',
                'name',
            ],
        ]);
    }

    private function getConfiguration(array $selection): array
    {
        $gql = (new Query('configuration'))
            ->setSelectionSet($this->createSelectionSet($selection));

        return $this->executeQuery($gql);
    }
}

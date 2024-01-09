<?php

namespace WpjShop\GraphQL\Services;

interface ServiceInterface
{
    public function setSelection(array $selection);

    public function get(int $id): ?array;

    public function list(int $offset = 0, int $limit = 100): array;

    public function update(int $id, array $data): array;

    public function create(array $data): array;

    public function translate(int $id, string $language, array $data): array;
}

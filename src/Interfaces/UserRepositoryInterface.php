<?php

namespace App\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    /**
     * @param User|array{
     *     country: string,
     *     city: string,
     *     is_active: bool,
     *     gender: string,
     *     birth_date: string,
     *     salary: float|int|string,
     *     has_children: bool,
     *     family_status: string,
     *     registration_date: string
     * } $user
     */
    public function save(User|array $user): bool;

    /**
     * @param array{
     *     city?: string,
     *     date_from?: string,
     *     date_to?: string
     * } $filters
     * @return array<int, array<string, mixed>>
     */
    public function findByFilters(array $filters): array;
}

<?php

namespace App;

use App\Core\DatabaseWrapper;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    private DatabaseWrapper $db;
    public function __construct(Database $db)
    {
        $this->db = $db->getWrapper();
    }

    public function save(User|array $user): bool
    {
        $data = $user instanceof User ? $user->toArray() : $user;

        $required = ['country', 'city', 'gender', 'birth_date', 'salary', 'family_status', 'registration_date'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new \InvalidArgumentException("Missing required field: $field");
            }
        }

        $orgId = $data['organization_id'] ?? 1;

        $sql = "INSERT INTO users (
            country, city, is_active, gender, birth_date, 
            salary, has_children, family_status, registration_date, 
            organization_id
        ) VALUES (
            :country, :city, :is_active, :gender, :birth_date, 
            :salary, :has_children, :family_status, :registration_date, 
            :organization_id
        )";

        return $this->db->execute($sql, [
            ':country'           => $data['country'],
            ':city'              => $data['city'],
            ':is_active'         => (int)($data['is_active'] ?? 0),
            ':gender'            => $data['gender'],
            ':birth_date'        => $data['birth_date'],
            ':salary'            => $data['salary'],
            ':has_children'      => (int)($data['has_children'] ?? 0),
            ':family_status'     => $data['family_status'],
            ':registration_date' => $data['registration_date'],
            ':organization_id'   => $orgId,
        ]);
    }

    /**
     * @param array{
     * city?: string,
     * date_from?: string,
     * date_to?: string,
     * organization_id?: int
     * } $filters
     * @return array<int, array<string, mixed>>
     */
    public function findByFilters(array $filters): array
    {
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (isset($filters['city'])) {
            $query .= " AND city = :city";
            $params['city'] = $filters['city'];
        }

        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query .= " AND registration_date BETWEEN :date_from AND :date_to";
            $params['date_from'] = $filters['date_from'];
            $params['date_to'] = $filters['date_to'];
        }

        if (isset($filters['organization_id'])) {
            $query .= " AND organization_id = :organization_id";
            $params['organization_id'] = (int)$filters['organization_id'];
        }

        return $this->db->fetchAll($query, $params);
    }
}

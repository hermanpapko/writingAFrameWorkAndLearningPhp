<?php

namespace App;

use App\Interfaces\UserRepositoryInterface;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private PDO $db;
    public function __construct(Database $db)
    {
        $this->db = $db->getConnection();
    }
    /**
     * @param array{
     *     country: string,
     *     city: string,
     *     is_active: bool,
     *     gender: string,
     *     birth_date: string,
     *     salary: float|int|string,
     *     has_children: bool,
     *     family_status: string,
     *     registration_date: string
     * } $data
     */
    public function save(array $data): bool
    {
        $sql = "INSERT INTO users (country, city, is_active, gender, birth_date, salary, has_children, family_status, registration_date) 
                VALUES (:country, :city, :is_active, :gender, :birth_date, :salary, :has_children, :family_status, :registration_date)";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':country', $data['country']);
        $stmt->bindValue(':city', $data['city']);
        $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
        $stmt->bindValue(':gender', $data['gender']);
        $stmt->bindValue(':birth_date', $data['birth_date']);
        $stmt->bindValue(':salary', $data['salary']);
        $stmt->bindValue(':has_children', $data['has_children'], PDO::PARAM_BOOL);
        $stmt->bindValue(':family_status', $data['family_status']);
        $stmt->bindValue(':registration_date', $data['registration_date']);

        return $stmt->execute();
    }
    /**
     * @param array{
     *     city?: string,
     *     date_from?: string,
     *     date_to?: string
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

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return (array) $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

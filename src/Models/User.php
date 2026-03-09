<?php

namespace App\Models;

use App\Core\Hydrator;

class User
{
    use Hydrator;

    public ?int $id = null;
    public string $country;
    public string $city;
    public bool $is_active;
    public string $gender;
    public string $birth_date;
    public float $salary;
    public bool $has_children;
    public string $family_status;
    public string $registration_date;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'country' => $this->country,
            'city' => $this->city,
            'is_active' => $this->is_active,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'salary' => $this->salary,
            'has_children' => $this->has_children,
            'family_status' => $this->family_status,
            'registration_date' => $this->registration_date,
        ];
    }
}

<?php

namespace App\Controllers;

use App\Core\DatabaseWrapper;
use App\Database;
use App\Interfaces\RendererInterface;

class OrganizationController
{
    private DatabaseWrapper $db;

    public function __construct(private RendererInterface $renderer)
    {
        $this->db = Database::getInstance()->getWrapper();
    }

    // List with pagination
    public function index(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $countResult = $this->db->fetch("SELECT COUNT(*) as total FROM organization");
        $total = $countResult['total'] ?? 0;
        $totalPages = ceil($total / $perPage);

        $sql = "SELECT * FROM organization ORDER BY id ASC LIMIT $perPage OFFSET $offset";
        $organizations = $this->db->fetchAll($sql);

        $this->renderer->render('organizations/index', [
            'organizations' => $organizations,
            'current_page' => $page,
            'total_pages' => $totalPages
        ]);
    }

    // Display create template
    public function create(): void
    {
        $this->renderer->render('organizations/form', ['organization' => null]);
    }

    // Handle creation
    public function store(): void
    {
        $name = $_POST['name'] ?? '';
        if ($name) {
            $this->db->execute("INSERT INTO organization (name) VALUES (:name)", [':name' => $name]);
        }
        header('Location: /organizations');
        exit;
    }

    // Display edit template (fills fields with data)
    public function edit(string $id): void
    {
        $org = $this->db->fetch("SELECT * FROM organization WHERE id = :id", ['id' => (int)$id]);
        if (!$org) {
            header('Location: /organizations');
            exit;
        }
        $this->renderer->render('organizations/form', ['organization' => $org]);
    }

    // Handle update
    public function update(string $id): void
    {
        $name = $_POST['name'] ?? '';
        if ($name) {
            $this->db->execute(
                "UPDATE organization SET name = :name, \"updatedAt\" = CURRENT_TIMESTAMP WHERE id = :id",
                ['name' => $name, 'id' => (int)$id]
            );
        }
        header('Location: /organizations');
        exit;
    }

    // Handle delete
    public function delete(string $id): void
    {
        $this->db->execute("DELETE FROM organization WHERE id = :id", ['id' => (int)$id]);
        header('Location: /organizations');
        exit;
    }
}

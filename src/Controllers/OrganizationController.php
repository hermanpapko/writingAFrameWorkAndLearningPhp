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

    private function requireAuth(): int
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        return (int)$_SESSION['user_id'];
    }

    // List with pagination
    public function index(): void
    {
        $ownerId = $this->requireAuth();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $countResult = $this->db->fetch(
            "SELECT COUNT(*) as total FROM organization WHERE owner_id = :owner_id",
            ['owner_id' => $ownerId]
        );
        $total = $countResult['total'] ?? 0;
        $totalPages = ceil($total / $perPage);

        $sql = "SELECT * FROM organization WHERE owner_id = :owner_id ORDER BY id ASC LIMIT $perPage OFFSET $offset";
        $organizations = $this->db->fetchAll($sql, ['owner_id' => $ownerId]);

        $this->renderer->render('organizations/index', [
          'organizations' => $organizations,
          'current_page' => $page,
          'total_pages' => $totalPages
        ]);
    }

    // Display create template
    public function create(): void
    {
        $this->requireAuth();
        $this->renderer->render('organizations/form', ['organization' => null]);
    }

    // Handle creation
    public function store(): void
    {
        $ownerId = $this->requireAuth();
        $name = trim($_POST['name'] ?? '');

        if ($name) {
            $this->db->execute(
                "INSERT INTO organization (name, owner_id) VALUES (:name, :owner_id)",
                ['name' => $name, 'owner_id' => $ownerId]
            );
        }
        header('Location: /organizations');
        exit;
    }

    // Display edit template (fills fields with data)
    public function edit(string $id): void
    {
        $ownerId = $this->requireAuth();

        $org = $this->db->fetch(
            "SELECT * FROM organization WHERE id = :id AND owner_id = :owner_id",
            ['id' => (int)$id, 'owner_id' => $ownerId]
        );

        if (!$org) {
            header('Location: /organizations');
            exit;
        }
        $this->renderer->render('organizations/form', ['organization' => $org]);
    }

    // Handle update
    public function update(string $id): void
    {
        $ownerId = $this->requireAuth();
        $name = trim($_POST['name'] ?? '');

        if ($name) {
            $this->db->execute(
                "UPDATE organization SET name = :name, \"updatedAt\" = CURRENT_TIMESTAMP WHERE id = :id AND owner_id = :owner_id",
                ['name' => $name, 'id' => (int)$id, 'owner_id' => $ownerId]
            );
        }
        header('Location: /organizations');
        exit;
    }

    // Handle delete
    public function delete(string $id): void
    {
        $ownerId = $this->requireAuth();

        $this->db->execute(
            "DELETE FROM organization WHERE id = :id AND owner_id = :owner_id",
            ['id' => (int)$id, 'owner_id' => $ownerId]
        );

        header('Location: /organizations');
        exit;
    }
}

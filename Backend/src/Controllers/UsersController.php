<?php
namespace App\Controllers;

use App\Models\User;
use Exception;

class UsersController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['perPage'] ?? 10;
        $search = $_GET['search'] ?? '';
        $users = $this->user->getAll($page, $perPage, $search);
        return json_encode($users);
    }

    public function show($id)
    {
        $user = $this->user->read($id);
        if ($user) {
            return json_encode($user);
        } else {
            return json_encode(['message' => 'User not found'], 404);
        }
    }

    public function store($userData)
    {
        $result = $this->user->create($userData);
        if (isset($result['errors'])) {
            return json_encode(['errors' => $result['errors']], 400);
        }
        return json_encode($result, 201);
    }

    public function update($id, $userData)
    {
        $result = $this->user->update($id, $userData);
        if (isset($result['errors'])) {
            return json_encode(['errors' => $result['errors']], 400);
        }
        return json_encode($result);
    }

    public function destroy($id)
    {
        $result = $this->user->delete($id);
        if ($result) {
            return json_encode(['message' => 'User and associated licenses deleted']);
        } else {
            return json_encode(['message' => 'User not found'], 404);
        }
    }
}
?>
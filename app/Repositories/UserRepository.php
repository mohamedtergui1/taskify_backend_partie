<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(User $User, array $data)
    {
        $User->update($data);
        return $User;
    }

    public function delete(User $User)
    {
        $User->delete();
    }

    public function getById(int $id)
    {
        return User::find($id);
    }
    public function getByEmail(string $email)
    {
        return User::where("email",$email)->firstOrFail();
    }

    public function getAll()
    {
        return User::all();
    }
}

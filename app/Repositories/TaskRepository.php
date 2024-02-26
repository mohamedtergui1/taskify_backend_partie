<?php

namespace App\Repositories;

use App\Models\Task;

class TaskRepository
{
    public function create(array $data)
    {
        return Task::create($data);
    }

    public function update(Task $Task, array $data)
    {
        $Task->update($data);
        return $Task;
    }

    public function delete(Task $Task)
    {
        $Task->delete();
    }

    public function getById(int $id)
    {
        return Task::findOrFail($id);
    }
    public function getByUserId(int $id)
    {
        return Task::where("user_id", $id)->get();
    }



    public function getAll()
    {
        return Task::all();
    }
}

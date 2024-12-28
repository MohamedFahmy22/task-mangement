<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Collection;

class TaskService
{
    public function getTasksForUser($user): Collection
    {
        return $user->isAdmin()
            ? Task::with(['creator', 'assignedUsers'])->latest()->get()
            : $user->assignedTasks()->with(['creator'])->latest()->get();
    }

    public function createTask(array $data, int $userId): Task
    {
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'created_by' => $userId,
        ]);

        $task->assignedUsers()->attach($data['users']);

        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        $task->update([
            'title' => $data['title'],
            'description' => $data['description'],
        ]);

        $task->assignedUsers()->sync($data['users']);

        return $task;
    }

    public function completeTask(Task $task): Task
    {
        $task->update(['status' => 'completed']);
        return $task;
    }
}

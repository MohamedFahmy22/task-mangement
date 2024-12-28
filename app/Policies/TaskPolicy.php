<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Task $task)
    {
        // Allow admins to update any task
        if ($user->role === 'admin') {
            return true;
        }

        // Allow users to update tasks assigned to them
        return $task->assignedUsers->contains($user);
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->isAdmin();
    }

    public function complete(User $user, Task $task): bool
    {
        return $task->assignedUsers->contains($user);
    }
}

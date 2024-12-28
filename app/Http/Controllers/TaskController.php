<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index(): View
    {
        if (auth()->user()->role === 'admin') {
            $tasks = Task::all();
        } else {
            $tasks = Task::whereHas('assignedUsers', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })->get();
        }
        return view('tasks.index', compact('tasks'));
    }

    public function create(): View
    {
        $users = User::where('role', 'user')->get();
        return view('tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->created_by = auth()->user()->id; // Set the created_by field
        $task->save();

        $task->assignedUsers()->sync($request->users);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task): View
    {
        $this->authorize('update', $task);
        $users = User::where('role', 'user')->get();
        return view('tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->title = $request->title;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->save();

        $task->assignedUsers()->sync($request->users);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function complete(Task $task)
    {
        $this->authorize('complete', $task);

        $this->taskService->completeTask($task);

        return redirect()->route('tasks.index')
            ->with('success', 'Task marked as completed.');
    }

    public function markAsCompleted(Task $task)
    {
        $task->update(['status' => 'completed']);
        return redirect()->back()->with('status', 'Task marked as completed!');
    }

    public function completedTasks(): View
    {
        if (auth()->user()->role === 'admin') {
            $tasks = Task::where('status', 'completed')->get();
        } else {
            $tasks = Task::where('status', 'completed')
                         ->whereHas('assignedUsers', function ($query) {
                             $query->where('user_id', auth()->user()->id);
                         })->get();
        }
        return view('tasks.completed', compact('tasks'));
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->status = $request->input('status');
        $task->save();

        return redirect()->route('tasks.index')->with('success', 'Task status updated successfully.');
    }
}

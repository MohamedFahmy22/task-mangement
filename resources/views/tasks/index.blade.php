<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tasks') }}
        </h2>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('tasks.create') }}" class="btn btn-primary d-flex align-items-center">
                <i class="fas fa-plus mr-2"></i> Create Task
            </a>
        @endif
    </x-slot>

    <div class="py-12">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @forelse($tasks as $task)
                            <div class="col-md-4 mb-4">
                                <div class="card {{ $task->status === 'completed' ? 'bg-light' : '' }}">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $task->title }}</h5>
                                        <p class="card-text">{{ $task->description }}</p>
                                        <div class="mb-2">
                                            <span class="text-muted">Assigned to:</span>
                                            <div class="d-flex flex-wrap">
                                                @foreach($task->assignedUsers as $user)
                                                    <span class="badge badge-secondary mr-1 mb-1">
                                                        {{ $user->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            @if(auth()->user()->role === 'admin')
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning d-flex align-items-center">
                                                    <i class="fas fa-edit mr-2"></i> Edit
                                                </a>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger d-flex align-items-center" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash mr-2"></i> Delete
                                                    </button>
                                                </form>
                                            @endif

                                            @if(auth()->user()->role === 'user' && $task->status !== 'completed')
                                                <form action="{{ route('tasks.updateStatus', $task) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()" class="form-control">
                                                        <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    </select>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                </div>
                                <div class="mt-2">
                                    <span class="text-sm {{ $task->status === 'completed' ? 'text-green-600' : 'text-blue-600' }}">
                                        Status: {{ ucfirst($task->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No tasks found.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
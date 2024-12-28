@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @foreach($tasks as $task)
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ $task->title }}</h5>
                        <p class="card-text">{{ $task->description }}</p>
                        <p class="card-text"><small class="text-muted">Assigned to: {{ $task->assigned_to }}</small></p>
                        <p class="card-text"><small class="text-muted">Status: {{ $task->status }}</small></p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

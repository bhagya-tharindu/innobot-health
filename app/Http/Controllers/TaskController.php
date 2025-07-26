<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Requests\CreateUpdateTaskRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Get all Tasks
     */
    public function index()
    {
        try {
            return response()->json(Task::all(), 200);
        } catch (\Throwable $th) {
            Log::error("TaskController@index: " . $th->getMessage());
            return response()->json(['error' => 'Failed to retrieve tasks'], 500);
        }
    }

    /**
     * Create a new Task
     */
    public function store(CreateUpdateTaskRequest $request)
    {
        try {
            $task = Task::create($request->validated());
            return response()->json($task, 201);
        } catch (\Throwable $th) {
            Log::error("TaskController@store: " . $th->getMessage());
            return response()->json(['error' => 'Failed to create tasks'], 500);
        }
    }

    /**
     * Update a task by ID
     */
    public function update(CreateUpdateTaskRequest $request, string $id)
    {

         try {
            $validatedData = $request->validated();
            $task = Task::findOrFail($id);
            $task->update($validatedData);
            return response()->json($task, 200);
        } catch (\Throwable $th) {
            Log::error("TaskController@update: " . $th->getMessage());
            return response()->json(['error' => 'Failed to update tasks'], 500);
        }
    }

    /**
     * delete a task by ID
     */
    public function destroy(string $id)
    {

        try {
            $task = Task::findOrFail($id);
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::error("TaskController@delete: " . $th->getMessage());
            return response()->json(['error' => 'Failed to delete tasks'], 500);
        }
    }
}

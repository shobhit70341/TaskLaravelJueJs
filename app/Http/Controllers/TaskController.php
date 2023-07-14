<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::latest()->get();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'due_date' => 'required|date',
        ]);

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'due_date' => 'required|date',
        ]);

        $task->update($request->all());
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');

        $query = Task::query();

        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $tasks = $query->latest()->get();

        return response()->json($tasks);
    }

    public function status(Request $request, $id)
    {
        $task = Task::find($id);

        if ($task) {
            $task->completed = $request->input('status') == 'completed' ? 1 : 0;
            $task->save();
            return response()->json($task);
        } else {
            return response()->json(['error' => 'Task not found'], 404);
        }
    }
}

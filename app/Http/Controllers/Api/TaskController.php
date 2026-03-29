<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    // POST /api/tasks
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tasks')->where(fn ($query) => $query->where('due_date', $request->due_date)),
            ],
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['required', 'date', 'after_or_equal:' . Carbon::today()->toDateString()],
        ]);

        $validated['status'] = $request->status ?? 'pending';

        $task = Task::create($validated);

        return response()->json($task, 201);
    }

    // GET /api/tasks
    public function index(Request $request)
    {
        $status = $request->query('status');

        $tasksQuery = Task::query();

        if ($status) {
            $tasksQuery->where('status', $status);
        }

        $priorityOrder = ['high', 'medium', 'low'];
        $tasksQuery->orderByRaw("FIELD(priority, '" . implode("','", $priorityOrder) . "') ASC")
                   ->orderBy('due_date', 'asc');

        $tasks = $tasksQuery->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                'message' => 'No tasks found',
                'data' => []
            ], 200);
        }

        return response()->json($tasks, 200);
    }
    // PATCH /api/tasks/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        $statusOrder = ['pending', 'in_progress', 'done'];
        $currentIndex = array_search($task->status, $statusOrder);

        if ($currentIndex === false || $currentIndex === count($statusOrder) - 1) {
            return response()->json([
                'message' => 'Task status cannot be updated further'
            ], 400);
        }

        $nextStatus = $statusOrder[$currentIndex + 1];

        $task->status = $nextStatus;
        $task->save();

        return response()->json([
            'message' => 'Task status updated successfully',
            'data' => $task
        ], 200);
    }

    // DELETE /api/tasks/{id}
    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }

        if ($task->status !== 'done') {
            return response()->json([
                'message' => 'Only tasks with status "done" can be deleted'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ], 200);
    }
    // GET /api/tasks/report?date=YYYY-MM-DD
    public function dailyReport(Request $request)
    {
        $date = $request->query('date');

        if (!$date || !Carbon::hasFormat($date, 'Y-m-d')) {
            return response()->json([
                'message' => 'Invalid or missing date. Format must be YYYY-MM-DD.'
            ], 400);
        }

        $tasks = Task::whereDate('due_date', $date)->get();

        $priorities = ['high', 'medium', 'low'];
        $statuses = ['pending', 'in_progress', 'done'];

        $summary = [];

        foreach ($priorities as $priority) {
            $summary[$priority] = [];
            foreach ($statuses as $status) {
                $summary[$priority][$status] = $tasks
                    ->where('priority', $priority)
                    ->where('status', $status)
                    ->count();
            }
        }

        return response()->json([
            'date' => $date,
            'summary' => $summary
        ], 200);
    }
}
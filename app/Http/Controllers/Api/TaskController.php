<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\TaskRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @group Tasks
 **/
class TaskController
{
    private function getMockTasks(): Collection
    {
        return collect([
            [
                'id' => 1,
                'title' => 'Design login page',
                'description' => 'Create a responsive login form with validation and error handling.',
                'due_date' => '2025-11-10',
                'is_completed' => false,
                'completed_at' => '2025-11-02 12:00:00',
                'created_at' => '2025-11-01 09:30:00',
                'updated_at' => '2025-11-03 14:45:00',
            ],
            [
                'id' => 2,
                'title' => 'Implement task CRUD API',
                'description' => 'Setup endpoints for create, read, update, and delete using mock data.',
                'due_date' => '2025-11-12',
                'is_completed' => false,
                'completed_at' => '2025-11-02 12:00:00',
                'created_at' => '2025-11-02 10:15:00',
                'updated_at' => '2025-11-04 16:20:00',
            ],
            [
                'id' => 3,
                'title' => 'Integrate frontend with backend',
                'description' => 'Connect Vue frontend with Laravel mock API using Axios.',
                'due_date' => '2025-11-14',
                'is_completed' => false,
                'completed_at' => '2025-11-02 12:00:00',
                'created_at' => '2025-11-03 11:00:00',
                'updated_at' => '2025-11-05 09:40:00',
            ],
            [
                'id' => 4,
                'title' => 'Add authentication flow',
                'description' => 'Mock the login endpoint and protect task routes with token or session.',
                'due_date' => '2025-11-08',
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => '2025-10-30 08:50:00',
                'updated_at' => '2025-11-02 17:10:00',
            ],
            [
                'id' => 5,
                'title' => 'Write documentation',
                'description' => 'Document setup steps, endpoints, and deployment instructions.',
                'due_date' => '2025-11-15',
                'is_completed' => false,
                'completed_at' => null,
                'created_at' => '2025-11-04 13:25:00',
                'updated_at' => '2025-11-05 18:00:00',
            ],
        ]);
    }

    /**
     * List tasks
     *
     * @authenticated
     *
     * @queryParam page integer Current page. Example: 1
     * @queryParam per_page integer Items per page. Example: 10
     * @queryParam filter[is_completed] boolean Filter completed. Example: false
     * @queryParam sort_by string Sort by. Example: created_at
     * @queryParam sort_order string Sort order. Example: desc
     *
     * @responseFile responses/tasks.index.json
     * @responseFile 401 responses/401.json
     */
    public function index(Request $request): JsonResponse
    {
        $tasks = $this->getMockTasks();
        $total = $tasks->count();

        $filterCompleted = $request->input('filter.is_completed');
        if (isset($filterCompleted)) {
            $tasks = $tasks->where('is_completed', filter_var($filterCompleted, FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = $request->input('sort_order', 'asc');
        $tasks = $tasks->sortBy($sortBy)->values();
        if ($sortOrder === 'desc') {
            $tasks = $tasks->reverse()->values();
        }

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $tasks = $tasks->slice($offset, $perPage)->values();

        return response()->json([
            'data' => $tasks,
            'meta' => [
                'total' => (int) $total,
                'per_page' => (int) $perPage,
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'from' => (int) $offset + 1,
                'to' => (int) min($offset + $perPage, $total),
            ]
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Create a task.
     *
     * @authenticated
     *
     * @bodyParam title string required The title of the task. Example: Task 6
     * @bodyParam description string required A short description. Example: Testing tas
     * @bodyParam due_date date required The task’s due date. Example: 2025-11-07
     *
     * @responseFile responses/tasks.store.json
     * @responseFile 401 responses/401.json
     * @responseFile 422 responses/tasks.store.422.json
     */
    public function store(TaskRequest $request)
    {
        $now = now()->toIso8601String(); // mock timestamp, eloquent will insert automatically

        $newTask = [
            'id' => $this->getMockTasks()->max('id') + 1,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'is_completed' => $request->is_completed ?? 0,
            'completed_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $this->getMockTasks()->push($newTask);
        // Task::create($newTask); // if we want to save in DB

        return response()->json([
            'data' => $newTask,
        ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Get a specific task
     *
     * @authenticated
     *
     * @pathParam task integer required The ID of the task. Example: 1
     *
     * @responseFile 200 responses/tasks.show.json
     * @responseFile 401 responses/401.json
     * @responseFile 404 responses/tasks.404.json
     */
    public function show(string $id)
    {
        $task = $this->getMockTasks()->where('id', $id)->first();

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return response()->json([
            'data' => $task,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Update a task.
     *
     * @authenticated
     *
     * @bodyParam title string required The title of the task. Example: Task 6
     * @bodyParam description string required A short description. Example: Testing tas
     * @bodyParam due_date date required The task’s due date. Example: 2025-11-07
     * @bodyParam is_completed boolean nullable The task’s status. Example: true
     * @bodyParam completed_date date nullable The task’s completed date. Example: 2025-11-07
     *
     * @responseFile responses/tasks.update.json
     * @responseFile 401 responses/401.json
     * @responseFile 422 responses/tasks.store.422.json
     */
    public function update(TaskRequest $request, string $id)
    {
        $task = $this->getMockTasks()->where('id', $id)->first();

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $task['title'] = $request->title;
        $task['description'] = $request->description;
        $task['due_date'] = $request->due_date;
        $task['is_completed'] = $request->is_completed;
        $task['completed_at'] = Carbon::parse($request->completed_at)->format('Y-m-d');
        $task['created_at'] = Carbon::parse($task['created_at'])->toIso8601String();
        $task['updated_at'] = now()->toIso8601String(); // mock updated_at, eloquent will update automatically

        $this->getMockTasks()->put($id, $task);
        // Task::find($id)->update($task); // if we want to update in DB

        return response()->json([
            'data' => $task,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Delete a specific task
     *
     * @authenticated
     *
     * @pathParam task integer required The ID of the task. Example: 1
     *
     * @responseFile 204 responses/tasks.destroy.json
     * @responseFile 404 responses/tasks.404.json
     */
    public function destroy(string $id)
    {
        $task = $this->getMockTasks()->where('id', $id)->first();

        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->getMockTasks()->forget($id);
        // Task::find($id)->delete(); // if we want to delete from DB

        return response()->json(null, 204);
    }
}

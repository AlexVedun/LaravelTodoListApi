<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequests\CreateTaskRequest;
use App\Http\Requests\TaskRequests\GetTasksRequest;
use App\Http\Requests\TaskRequests\TaskRequest;
use App\Http\Requests\TaskRequests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    private TaskRepositoryInterface $taskRepository;
    private ResponseService $responseService;

    public function __construct(
        TaskRepositoryInterface $taskRepository,
        ResponseService $responseService
    ){
        $this->taskRepository = $taskRepository;
        $this->responseService = $responseService;
    }

    public function all(GetTasksRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $filters = $request->only([
            'status',
            'title',
            'priority',
            'sort_by',
            'sort_direction',
        ]);

        $tasks = $this->taskRepository->getTasks($user->id, $filters);

        return response()->json(
            $this->responseService->getOkResponse(
                "User's tasks have been load successfully",
                TaskResource::collection($tasks)
            )
        );
    }

    public function get(TaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $taskId = $request->get('task_id');

        $task = $this->taskRepository->getTask($taskId);

        if ($user->cannot('view', $task)) {
            return response()->json(
                $this->responseService->getErrorResponse('User not allowed to view this task'),
                Response::HTTP_FORBIDDEN
            );
        }

        return response()->json(
            $this->responseService->getOkResponse(
                'Task has been found successfully',
                TaskResource::make($task)
            )
        );
    }

    public function create(CreateTaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $parentId = $request->get('parent_id');
        $taskData = $request->only([
            'description',
            'title',
            'priority',
            'parent_id',
        ]);
        $taskData['user_id'] = $user->id;

        if ($parentId) {
            $task = $this->taskRepository->getTask($parentId);
            if ($user->cannot('update', $task)) {
                return response()->json(
                    $this->responseService->getErrorResponse('User not allowed to create subtask for this task'),
                    Response::HTTP_FORBIDDEN
                );
            }
        }

        $task = $this->taskRepository->createTask($taskData);

        if ($task == null) {
            return response()->json(
                $this->responseService->getErrorResponse('Cannot create task due to internal error'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            $this->responseService->getOkResponse(
                'Task created successfully',
                TaskResource::make($task)
            )
        );
    }

    public function update(UpdateTaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $taskId = $request->get('task_id');
        $taskData = $request->only([
            'priority',
            'title',
            'description'
        ]);
        $taskData['user_id'] = $user->id;

        $task = $this->taskRepository->getTask($taskId);

        if ($user->cannot('update', $task)) {
            return response()->json(
                $this->responseService->getErrorResponse('User not allowed to update the task'),
                Response::HTTP_FORBIDDEN
            );
        }

        $task = $this->taskRepository->updateTask($task, $taskData);

        if ($task == null) {
            return response()->json(
                $this->responseService->getErrorResponse('Cannot update task due to internal error'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            $this->responseService->getOkResponse(
                'Task has been updated successfully',
                TaskResource::make($task)
            )
        );
    }

    public function delete(TaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $taskId = $request->get('task_id');

        $task = $this->taskRepository->getTask($taskId);

        if ($user->cannot('delete', $task)) {
            return response()->json(
                $this->responseService->getErrorResponse('User not allowed to delete this task'),
                Response::HTTP_FORBIDDEN
            );
        }

        if (!$this->taskRepository->deleteTask($task)) {
            return response()->json(
                $this->responseService->getErrorResponse('Cannot delete selected task due to internal error'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            $this->responseService->getOkResponse('Task has been deleted successfully')
        );
    }

    public function complete(TaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $taskId = $request->get('task_id');

        $task = $this->taskRepository->getTask($taskId);

        if ($user->cannot('update', $task)) {
            return response()->json(
                $this->responseService->getErrorResponse('User not allowed to complete this task'),
                Response::HTTP_FORBIDDEN
            );
        }

        if ($user->cannot('complete', $task)) {
            return response()->json(
                $this->responseService->getErrorResponse('Cannot complete task with uncompleted subtasks'),
                Response::HTTP_FORBIDDEN
            );
        }

        $task = $this->taskRepository->completeTask($task);

        if (!$task) {
            return response()->json(
                $this->responseService->getErrorResponse('Cannot complete task due to internal error'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            $this->responseService->getOkResponse(
                'Task has been completed successfully',
                TaskResource::make($task)
            )
        );
    }
}

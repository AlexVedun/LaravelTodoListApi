<?php

namespace App\Http\Controllers;

use App\DTO\TaskDTO\TaskDTO;
use App\DTO\TaskDTO\TaskFilterDTO;
use App\Http\Requests\TaskRequests\TaskRequest;
use App\Http\Requests\TaskRequests\TasksFilterRequest;
use App\Http\Resources\TaskResource;
use App\Interfaces\TaskRepositoryInterface;
use App\Models\User;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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

    public function index(TasksFilterRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        $taskFilterDTO = TaskFilterDTO::fromRequest($request);

        $tasks = $this->taskRepository->getTasks($user->id, $taskFilterDTO);

        return response()->json(
            $this->responseService->getOkResponse(
                "User's tasks have been load successfully",
                TaskResource::collection($tasks)
            )
        );
    }

    public function show(int $id): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        Auth::login($user);

        $task = $this->taskRepository->getTask($id);

        $this->authorize('view', $task);

        return response()->json(
            $this->responseService->getOkResponse(
                'Task has been found successfully',
                TaskResource::make($task)
            )
        );
    }

    public function store(TaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        Auth::login($user);
        $parentId = $request->get('parent_id');
        $taskData = TaskDTO::fromRequest($request, $user->id);

        if ($parentId) {
            $parentTask = $this->taskRepository->getTask($parentId);
            $this->authorize('update', $parentTask);
        }

        $task = $this->taskRepository->createTask($taskData);

        return response()->json(
            $this->responseService->getOkResponse(
                'Task created successfully',
                TaskResource::make($task)
            )
        );
    }

    public function update(int $id, TaskRequest $request): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        Auth::login($user);
        $taskData = TaskDTO::fromRequest($request, $user->id);

        $task = $this->taskRepository->getTask($id);

        $this->authorize('update', $task);

        $task = $this->taskRepository->updateTask($task, $taskData);

        return response()->json(
            $this->responseService->getOkResponse(
                'Task has been updated successfully',
                TaskResource::make($task)
            )
        );
    }

    public function destroy(int $id): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        Auth::login($user);

        $task = $this->taskRepository->getTask($id);

        $this->authorize('delete', $task);

        $this->taskRepository->deleteTask($task);

        return response()->json(
            $this->responseService->getOkResponse('Task has been deleted successfully')
        );
    }

    public function complete(int $id): JsonResponse
    {
        // In real application here we obtain the model of user authenticated before,
        // for example, by using Auth::user(), or get user id in request
        $user = User::find(1);
        Auth::login($user);

        $task = $this->taskRepository->getTask($id);

        $this->authorize('update', $task);
        $this->authorize('complete', $task);

        $task = $this->taskRepository->completeTask($task);

        return response()->json(
            $this->responseService->getOkResponse(
                'Task has been completed successfully',
                TaskResource::make($task)
            )
        );
    }
}

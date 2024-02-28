<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Resources\TaskResource;


class TaskController extends Controller
{


    private $TaskRepository;
    function __construct(TaskRepository $taskRepository)
    {
        $this->TaskRepository = $taskRepository;
        $this->middleware('auth:sanctum');
    }

    function index()
    {
        $tasks = $this->TaskRepository->getByUserId(Auth::user()->id);
        if ($tasks->count())
            return response()->json([
                'status' => true,
                "message" => "data found",

                "data" => TaskResource::collection($tasks)
            ]);
        else
            return response()->json([
                "status" => false
                ,
                "message" => "you don't have any tasks"
            ]);
    }

    function store(TaskRequest $request)
    {

        $task = $this->TaskRepository->create([
            "name" => $request->name
            ,
            "description" => $request->description
            ,
            "start_date" => $request->start_date
            ,
            "end_date" => $request->end_date
            ,

            "user_id" => Auth::user()->id
        ]);

        return response()->json([
            "status" => true
            ,
            "task" => TaskResource::collection([$task])
            ,
            "message" => "task created with success"
        ], 201);

    }

   function show(int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);

            if ($task) {
                $this->authorize('show', $task);
                return response()->json([
                    "status" => true,
                    "data" => new TaskResource($task),
                    'message' => "the task is found"
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    'message' => "you don't have any tasks",
                    'data' => []
                ], 404);
            }
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }

    function update(TaskRequest $request, int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);
            $this->authorize('update', $task);
            $task = $this->TaskRepository->update($task, [
                "name" => $request->name
                ,
                "status" => $request->status
                ,
                "description" => $request->description
                ,
                "start_date" => $request->start_date
                ,
                "end_date" => $request->end_date
                ,
                "user_id" => Auth::user()->id
            ]);

            return response()->json([

                "status" => true
                ,
                "data" => new TaskResource($task)
                ,
                "message" => "task updates success"

            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }


    function destroy(int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);
            $this->authorize('delete', $task);
            if ($task) {
                $this->TaskRepository->delete($task);
                return response()->json([
                    "status" => true
                    ,
                    "message" => "task deleted with success"
                ], 200);
            }
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }




    function changeTaskToToDo(int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);
            $this->authorize('update', $task);
            $task = $this->TaskRepository->update($task, [

                "status" => "to do"

            ]);
            return response()->json([

                "status" => true
                ,
                "task" => TaskResource::collection([$task])
                ,
                "message" => "task updates success"

            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }





    function changeTaskToInProgress(int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);
            $this->authorize('update', $task);
            $task = $this->TaskRepository->update($task, [

                "status" => "in progress"
            ]);
            return response()->json([

                "status" => true
                ,
                "task" => TaskResource::collection([$task])
                ,
                "message" => "task updates success"

            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }










   function changeTaskToCompleted(int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);
            $this->authorize('update', $task);
            $task = $this->TaskRepository->update($task, [

                "status" => "completed"

            ]);
            return response()->json([

                "status" => true
                ,
                "task" => TaskResource::collection([$task])
                ,
                "message" => "task updates success"

            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }




}

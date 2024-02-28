<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Resources\TaskResource;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="Endpoints for managing tasks"
 * )
 */
class TaskController extends Controller
{


    private $TaskRepository;
    function __construct(TaskRepository $taskRepository)
    {
        $this->TaskRepository = $taskRepository;
        $this->middleware('auth:sanctum');
    }



     /**
     * @OA\Get(
     *     path="/api/task",
     *     summary="List all tasks",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="A list of tasks",
     *         )
     *     ),
     *     @OA\Response(response="404", description="No tasks found")
     * )
     */


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

     /**
     * @OA\Post(
     *     path="/api/task",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for creating a new task"
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully"
     *     ),
     *     @OA\Response(response="400", description="Bad request"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
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
      /**
     * @OA\Get(
     *     path="/api/task/{taskId}",
     *     summary="Get details of a specific task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the task to retrieve"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task details"
     *     ),
     *     @OA\Response(response="404", description="Task not found")
     * )
     */
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


     /**
     * @OA\Put(
     *     path="/api/task/{taskId}",
     *     summary="Update a specific task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="taskId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the task to update"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for updating the task"
     *     ),
     *     @OA\Response(response="200", description="Task updated successfully"),
     *     @OA\Response(response="404", description="Task not found"),
     *     @OA\Response(response="422", description="Validation error")
     * )
     */
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

       /**
 * @OA\Delete(
 *     path="/api/task/{taskId}",
 *     summary="Delete a specific task",
 *     tags={"Tasks"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="taskId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the task to delete"
 *     ),
 *     @OA\Response(response="200", description="Task deleted successfully"),
 *     @OA\Response(response="404", description="Task not found")
 * )
 */
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

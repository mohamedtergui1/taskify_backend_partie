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
    /**
 * @OA\Get(
 *      path="/api/tasks",
 *      operationId="getTasks",
 *      tags={"Tasks"},
 *      summary="Get user tasks",
 *      description="Returns tasks of the authenticated user.",
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="status",
 *                  type="boolean",
 *                  example="true"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="data found"
 *              ),
 *              @OA\Property(
 *                  property="data",
 *                  type="array",
 *                  @OA\Items(ref="#/components/schemas/TaskResource")
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="No tasks found",
 *          @OA\JsonContent(
 *              @OA\Property(
 *                  property="status",
 *                  type="boolean",
 *                  example="false"
 *              ),
 *              @OA\Property(
 *                  property="message",
 *                  type="string",
 *                  example="you don't have any tasks"
 *              )
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
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
 *      path="/api/tasks",
 *      operationId="createTask",
 *      tags={"Tasks"},
 *      summary="Create a new task",
 *      description="Creates a new task for the authenticated user.",
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"name", "description", "start_date", "end_date"},
 *              @OA\Property(property="name", type="string", example="Task Name"),
 *              @OA\Property(property="description", type="string", example="Task Description"),
 *              @OA\Property(property="start_date", type="string", format="date", example="2024-02-27"),
 *              @OA\Property(property="end_date", type="string", format="date", example="2024-03-05"),
 *          ),
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Task created successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="task", type="object", ref="#/components/schemas/TaskResource"),
 *              @OA\Property(property="message", type="string", example="Task created with success")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
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
            "task" => TaskResource::collection($task)
            ,
            "message" => "task created with success"
        ], 201);

    }


    /**
 * @OA\Get(
 *      path="/api/tasks/{id}",
 *      operationId="getTaskById",
 *      tags={"Tasks"},
 *      summary="Get a task by ID",
 *      description="Returns a task by its ID.",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          description="ID of the task",
 *          required=true,
 *          @OA\Schema(type="integer", format="int64")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful operation",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="task", ref="#/components/schemas/Task"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=404,
 *          description="Task not found",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="Task not found")
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="You are not authorized to view this task")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
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
                    "task" => $task
                ], 200);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Task not found"
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
 *      path="/api/tasks/{id}",
 *      operationId="updateTask",
 *      tags={"Tasks"},
 *      summary="Update a task by ID",
 *      description="Updates a task by its ID.",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          description="ID of the task",
 *          required=true,
 *          @OA\Schema(type="integer", format="int64")
 *      ),
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"name", "status", "description", "start_date", "end_date"},
 *              @OA\Property(property="name", type="string", example="Updated Task Name"),
 *              @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="in_progress"),
 *              @OA\Property(property="description", type="string", example="Updated Task Description"),
 *              @OA\Property(property="start_date", type="string", format="date", example="2024-02-27"),
 *              @OA\Property(property="end_date", type="string", format="date", example="2024-03-05"),
 *          ),
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Task updated successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="task", ref="#/components/schemas/Task"),
 *              @OA\Property(property="message", type="string", example="Task updated successfully")
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="You are not authorized to update this task")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
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
                "task" => $task
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
 *      path="/api/tasks/{id}",
 *      operationId="deleteTask",
 *      tags={"Tasks"},
 *      summary="Delete a task by ID",
 *      description="Deletes a task by its ID.",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          description="ID of the task",
 *          required=true,
 *          @OA\Schema(type="integer", format="int64")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Task deleted successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="message", type="string", example="Task deleted with success")
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="You are not authorized to delete this task")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */

    function delete(int $id)
    {
        try {
            $task = $this->TaskRepository->getById($id);
            $this->authorize('delete', $task);
            $this->TaskRepository->delete($task);
            return response()->json([
                "status" => true
                ,
                "message" => "task deleted with success"
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 403);
        }
    }




    /**
 * @OA\Put(
 *      path="/api/changeTaskToToDo/{id}",
 *      operationId="changeTaskToToDo",
 *      tags={"Tasks"},
 *      summary="Change task status to 'to do'",
 *      description="Changes the status of a task to 'to do' by its ID.",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          description="ID of the task",
 *          required=true,
 *          @OA\Schema(type="integer", format="int64")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Task status updated successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="task", ref="#/components/schemas/Task"),
 *              @OA\Property(property="message", type="string", example="Task status updated successfully")
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="You are not authorized to update this task")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */

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
                "task" => TaskResource::collection($task)
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
 * @OA\Put(
 *      path="/api/changeTaskToInProgress/{id}",
 *      operationId="changeTaskToToDo",
 *      tags={"Tasks"},
 *      summary="Change task status to 'to do'",
 *      description="Changes the status of a task to 'to do' by its ID.",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          description="ID of the task",
 *          required=true,
 *          @OA\Schema(type="integer", format="int64")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Task status updated successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="task", ref="#/components/schemas/Task"),
 *              @OA\Property(property="message", type="string", example="Task status updated successfully")
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="You are not authorized to update this task")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */

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
                "task" => TaskResource::collection($task)
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
 * @OA\Put(
 *      path="/api/changeTaskToCompleted/{id}",
 *      operationId="changeTaskToToDo",
 *      tags={"Tasks"},
 *      summary="Change task status to 'to do'",
 *      description="Changes the status of a task to 'to do' by its ID.",
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          description="ID of the task",
 *          required=true,
 *          @OA\Schema(type="integer", format="int64")
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Task status updated successfully",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="true"),
 *              @OA\Property(property="task", ref="#/components/schemas/Task"),
 *              @OA\Property(property="message", type="string", example="Task status updated successfully")
 *          )
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="boolean", example="false"),
 *              @OA\Property(property="message", type="string", example="You are not authorized to update this task")
 *          )
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */

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
                "task" => TaskResource::collection($task)
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

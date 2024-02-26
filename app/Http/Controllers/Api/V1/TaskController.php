<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\Auth;

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
        $taseks =$this->TaskRepository->getByUserId(Auth::user()->id);
        if($taseks->count())
        return response()->json([
            'status' => true,
            "tasks" => $taseks
                ]);
        else return response()->json([
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
            "task" => $task
            ,
            "message" => "task created with success"
        ], 201);

    }
    function show(int $id)
    {
        if ($task = $this->TaskRepository->getById($id)) {
            $this->authorize('show', $task);

            return response()->json([
                "status" => true
                ,
                "task" => $task
            ]);
        } else {
            response()->json([
                "status" => false
                ,
                "message" => "task not found"
            ]);
        }
    }

    function update(TaskRequest $request, int $id)
    {
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
    }


    function delete(int $id)
    {
        $task = $this->TaskRepository->getById($id);
        $this->authorize('delete', $task);
        $this->TaskRepository->delete($task);
        return response()->json([
            "status" => true
            ,
            "message" => "task deleted with success"
        ]);
    }


}

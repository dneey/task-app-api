<?php

namespace App\Http\Controllers;

use App\Notifications\TodoCreatedNotification;
use Log;
use App\Todo;
use Validator;
use App\User;

class TodosController extends Controller
{
    public function index()
    {
        $todos = Todo::latest()->paginate(10);
        if ($todos->isEmpty()) {
            return response()->json(['responseMessage' => 'Validation failed', 'responseCode' => '400', 'data' => []]);
        }
        Log::info('getting tasks success');
        return response()->json(['responseMessage' => 'Validation failed', 'responseCode' => '200', 'data' => $todos]);
    }

    public function store()
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'description' => 'nullable'
        ]);
        if ($validator->fails()) {
            Log::info('Validation has failed at creating a todo');
            return response()->json(['responseMessage' => 'Validation failed', 'responseCode' => '400', 'data' => $validator->errors()]);
        }
        $todo = [
            'completed' => false,
            'title' =>  request()->title,
            'description' => request()->description,
            'user_id' => auth()->id() ?? User::firstOrCreate(['email' => 'yarteyd@emial', 'password' => 'test', 'name' => 'test'])->id
        ];
        $todo = Todo::create($todo);
        //auth()->user()->notify(new TodoCreatedNotification);
        Log::info('New task created');
        return response()->json(['responseMessage' => 'Successfully added a task', 'responseCode' => '200', 'data' => $todo]);
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        Log::info('Task deleted');
        return response()->json(['responseMessage' => 'Successfully deleted task/todo', 'responseCode' => '200', 'data' => $todo]);
    }

    public function update(Todo $todo)
    {
        $todo->update(request()->only('title', 'completed', 'description'));
        Log::info('update tasks success');
        return response()->json(['responseMessage' => 'Successfully deleted task/todo', 'responseCode' => '200', 'data' => $todo]);
    }
}

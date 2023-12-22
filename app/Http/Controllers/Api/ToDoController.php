<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponser;
use App\Models\ToDo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ToDoController extends Controller
{
    use ApiResponser;

    public function create_todo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:240',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        $new_todo = ToDo::create(
            [
                'user_id' => $request->user_id,
                'title' => $request->title,
                'description' => $request->description,
            ]
        );

        return $this->success(
            ['todo' => $new_todo], __('apis.todos.save')
        );

    }

    public function view_todo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'todo_id' => 'required|exists:to_dos,id',
        ]);

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        $view_todo = ToDo::find($request->todo_id)->get();

        if (! $view_todo) {
            return $this->error(__('apis.todos.notFound'), 200);
        }

        return $this->success(['viewToDo' => $view_todo], __('apis.todos.view'));
    }

    public function update_todo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'description' => 'required|string|max:240',
            'user_id' => 'required|exists:users,id',
            'todo_id' => 'required|exists:to_dos,id',
        ]);

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        $todo = ToDo::find($request->todo_id);

        if (! $todo) {
            return $this->error(__('apis.todos.notFound'), 200);
        }

        if ($request->user_id != $todo->user_id) {

            return $this->error(__('apis.todos.unauthorized'), 200);

        }

        $todo->fill([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return $this->success(['viewToDo' => $todo], __('apis.todos.update'));

    }

    public function delete_todo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'todo_id' => 'required|exists:to_dos,id',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error('', 401, $validator->errors());
        }

        $todo = ToDo::find($request->todo_id);

        if (! $todo) {
            return $this->error(__('apis.todos.notFound'), 200);
        }

        if ($request->user_id != $todo->user_id) {
            return $this->error(__('apis.todos.unauthorized'), 200);
        }

        $todo->delete();

        return $this->success(__('apis.todos.delete'));
    }

    public function list_todo(User $user, Request $request)
    {
        if (! $user) {
            return $this->error(__('apis.user.userNotFound'), 200);
        }

        $searchTodo = $request->searchTodo;

        $todosQuery = $user->todos();

        if ($searchTodo) {
            $todosQuery->where('title', 'like', '%'.$searchTodo.'%');
        }

        $todos = $todosQuery->paginate(10);

        return $this->success(
            [
                'status' => 'success',
                'todo_list' => $todos,
            ],
            __('apis.user.list')
        );
    }
}

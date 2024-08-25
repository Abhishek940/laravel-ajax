<?php

namespace App\Http\Controllers;

use App\Models\cr;
use Illuminate\Http\Request;
use App\Models\ToDo;
use Illuminate\Support\Facades\Validator;
class ToDoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('ToDo.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ToDolist()
    {
        $todos = ToDo::select('id','task','status')->where('status',1)->get();
        return response()->json([
            'todos'=>$todos,
        ]);
    }

    public function allTodoData()
    {
        $todos = ToDo::select('id','task','status')->get();
        return response()->json([
            'todos'=>$todos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'task' => 'required|max:10|unique:to_dos,task',
        ],
         [
            'task.unique' => 'The Task you entered already exists. Please enter a different task.',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status'=>400,
                'errors'=>$validator->messages()
            ]);
        }
        else
        {
            $toDo = new ToDo;
            $toDo->task = $request->input('task');
            $toDo->status = 1; // 1 = pending
            $toDo->save();
            return response()->json([
                'status'=>200,
                'message'=>'Task Added Successfully.'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function show(cr $cr)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function edit(cr $cr)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, cr $cr)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\cr  $cr
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = ToDo::find($id);
        if($data)
        {
            $data->delete();
            return response()->json([
                'status'=>200,
                'message'=>'Record Deleted Successfully.'
            ]);
        }
        else
        {
            return response()->json([
                'status'=>404,
                'message'=>'No Data Found.'
            ]);
        }
    }

       public function updateTaskStatus(Request $request)
        {
            $task = ToDo::find($request->id);
            if ($task) {
                $task->status = $request->status;
                $task->save();
                return response()->json(['status' => 200, 'message' => 'Status updated successfully.']);
            } else {
                return response()->json(['status' => 404, 'message' => 'Task not found.']);
            }
        }

}

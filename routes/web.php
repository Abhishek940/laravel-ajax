<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ToDoController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Route::get('/', function () {
    return view('index');
}); */



Route::get('/', [ToDoController::class, 'index']);
Route::post('/submit/toDo', [ToDoController::class, 'store']);
Route::get('/toDolist', [ToDoController::class, 'ToDolist']);
Route::get('/alltoDolist', [ToDoController::class, 'allTodoData']);
Route::post('/updateTaskStatus', [ToDoController::class, 'updateTaskStatus']);
Route::delete('/delete/toDo/{id}', [ToDoController::class, 'destroy']);


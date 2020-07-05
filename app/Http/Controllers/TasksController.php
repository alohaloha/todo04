<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 以下2行追加
use Validator;//入力チェックしますよー宣言
use App\Task;//モデルTask.phpを使いますよー宣言
use Auth;//Authクラスを使用しますよー宣言

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     //__constructがついているのは、コントローラ内の関数が実行される前に必ず実行される関数
     public function __construct()
     {
       $this->middleware('auth');
     }
     
    public function index()
    {
    // $tasks = Task::orderBy('deadline', 'asc')->get();
    // 下記のように変更
    $tasks = Task::where('user_id',Auth::user()->id)
            ->orderBy('deadline', 'asc')
            ->get();
    // 以降は変更なし
    return view('tasks', [
      'tasks' => $tasks
    ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
      //バリデーション
      $validator = Validator::make($request->all(), [
        'task' => 'required|max:255',
        'deadline' => 'required',
      ]);
      //バリデーション:エラー
      if ($validator->fails()) {
        return redirect()
          ->route('task.index')
          ->withInput()
          ->withErrors($validator);
      }
      // Eloquentモデル
      $task = new Task;
      // 下記1行を追加
      $task->user_id = Auth::user()->id;
      // 以降変更なし
      $task->task = $request->task;
      $task->deadline = $request->deadline;
      $task->comment = $request->comment;
      $task->save();
      //「/」ルートにリダイレクト
      return redirect()->route('tasks.index');    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
          $task = Task::find($id);
          // ddd($id);
          return view('taskedit', ['task' => $task]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
          //バリデーション
          $validator = Validator::make($request->all(), [
            'task' => 'required|max:255',
            'deadline' => 'required',
          ]);
          //バリデーション:エラー
          if ($validator->fails()) {
            return redirect()
              ->route('tasks.edit', $id)
              ->withInput()
              ->withErrors($validator);
          }
          //データ更新処理
          $task = Task::find($id);
          $task->task   = $request->task;
          $task->deadline = $request->deadline;
          $task->comment = $request->comment;
          $task->save();
          return redirect()->route('tasks.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
          $task = Task::find($id);
          $task->delete();
          return redirect()->route('tasks.index');
    }
}

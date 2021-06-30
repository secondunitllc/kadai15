<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザの投稿の一覧を作成日時の降順で取得
            // （後のChapterで他ユーザの投稿も取得するように変更しますが、現時点ではこのユーザの投稿のみ取得します）
            //$tasks = Task::all();
            $tasks = $user->tasks()->get();

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            
          
        }
        
        // Welcomeビューでそれらを表示
        return view('welcome', $data);
        
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       if (\Auth::check()) {
        $user = \Auth::user();
        $task = new Task;
        return view('tasks.create', [
            'user' => $user,
            'task' => $task,
        ]);
        }
        
        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (\Auth::check()) {
        $user =  $request->user;
        // バリデーション
        $request->validate([
            'content' => 'required',
            'status' => 'required|max:10'  // 追加
        ]);
        
        
        $task = new Task;
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        $task->user_id = $request->user()->id;
        $task->save();
         }
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
        
        //$user = \Auth::user();
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) {
        return view('tasks.show', [
            //'user' => $user,
            'task' => $task,
        ]);
        }
        
        return redirect('/');
    
    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        //$user = \Auth::user();
        $task = Task::findOrFail($id);

        if (\Auth::id() === $task->user_id) {
        return view('tasks.edit', [
            //'user' => $user,
            'task' => $task,
        ]);
        }
        
        return redirect('/');
    }

    /**s
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$user = \Auth::user();
        if (\Auth::check()) {
        // バリデーション
        $request->validate([
            'content' => 'required',
            'status' => 'required|max:10'   // 追加
        ]);

        $task = Task::findOrFail($id);
        $task->status = $request->status;    // 追加
        $task->content = $request->content;
        //$task->user_id = $request->user()->id;
        $task->save();
        }
        // トップページへリダイレクトさせる
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    
    
    // idの値で投稿を検索して取得
        $task = Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        // 前のURLへリダイレクトさせる
        return redirect('/');
    }}


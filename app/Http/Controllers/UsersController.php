<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
class UsersController extends Controller
{
    public function __construct()
    {
        //除了当前动作以外的其他动作都需要登录
        $this->middleware('auth',[
           'except' =>['create','show','store','index','confirmEmail']
        ]);
        //
        $this->middleware('guest',[
           'only' =>['create']
        ]);
        //限流 一个小时只能提交10次请求
        $this->middleware('throttle:10,60',[
            'only'=>['store']
        ]);
    }
    //显示所有的用户
    public function index(){
        $users = User::paginate(6);
        return view('users.index',compact('users'));
    }

    //创建用户界面
    public function create()
    {
        return view('users.create');
    }
    //显示个人信息
    public function show(User $user)
    {
        $statuses = $user->statuses()
                        ->orderBy('created_at','desc')
                        ->paginate(10);


        return view('users.show', compact('user','statuses'));
    }
    //创建用户
    public function store(Request $request){
        $this->validate($request,[
           'name'=>'required|unique:users|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'password' => bcrypt($request->password)
        ]);

        //Auth::login($user);
        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        //return redirect()->route('users.show',[$user]);
        return redirect('/');
    }

    //修改个人用户信息页面
    public function edit(User $user){
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }
    //更新用户信息
    public function update(User $user,Request $request){
        $this->authorize('update', $user);
        $this->validate($request,[
            'name' =>'required|max:50',
            'password' => 'nullable|confirmed|min:6'
        ]);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功');
        return redirect()->route('users.show',$user->id);
    }
    //删除用户
    public function destroy(User $user){
        $this->authorize('destroy', $user);
        $user->delete();
        session()->flash('success','成功删除用户！');
        return back();//返回上一个页面
    }

    //邮件 发送
    protected function sendEmailConfirmationTo($user){
        $view = 'emails.confirm';
        $data = compact('user');
        $name = 'Xiaocainiao';
        $to = $user->email;
        $subject = '"感谢注册 Weibo 应用！请确认你的邮箱。';

        Mail::send($view, $data, function ($message) use ( $name, $to, $subject) {
            $message->to($to)->subject($subject);
        });
    }
    //邮箱激活
    public function confirmEmail($token){
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success','恭喜你，激活成功!');
        return redirect()->route('users.show',[$user]);
    }
}

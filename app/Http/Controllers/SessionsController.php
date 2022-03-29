<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SessionsController extends Controller
{
    //当类被创建时执行
    public function __construct()
    {
        //只让未登录用户访问登录页面和注册页面。
        $this->middleware('guest',[
           'only' =>['create']
        ]);
    }

    //
    public function create(){
        return view('sessions.create');
    }

    public function store(Request $request){
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required'
        ]);
        if(Auth::attempt($credentials,$request->has('remember'))){
            //登录成功后的操作
            session()->flash('success','欢迎回来！');
            $fallback = route('users.show',[Auth::user()]);
            /*intended 方法，该方法可将页面重定向到上一次请求尝试访问的页面上，
            并接收一个默认跳转地址参数，当上一次请求记录为空时，跳转到默认地址上。*/
            return redirect()->intended($fallback);
        }else{
            //登录失败后的操作
            session()->flash('danger','很抱歉，您的邮箱和密码不匹配');
            return redirect()->back()->withInput();
        }

    }

    public function destroy(){
        Auth::logout();
        session()->flash('success','您已成功退出');
        return redirect('login');
    }
}

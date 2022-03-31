<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Status;
use function Symfony\Component\Console\Style\comment;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    //boot 方法会在用户模型类完成初始化之后进行加载
    public static function boot(){
        parent::boot();
        //事件监听
        static::creating(function($user){
            $user->activation_token = Str::random(10);
        });
    }


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function gravatar($size = '100'){
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "https://cdn.v2ex.com/gravatar/$hash?s=$size";
    }

    //获取当前用户的所有微博
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }
    //获取当前用户所有的微博
    public function feed(){
        return $this->statuses()->orderBy('created_at','desc');
    }
    //获取粉丝关系列表
    public function followers(){
        return $this->belongsToMany(User::class,'followers','user_id','follower_id');
    }
    //获取关注人列表
    public function followings(){
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }
    //关注
    public function follow($user_ids){
        if(! is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids,false);
    }
    //取消关注
    public function unfollow($user_ids){
        if(! is_array($user_ids)){
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }
    //判断当前用户是否关注了指定用户
    public function isFollowing($user_id){
        return $this->followings->contains($user_id);
    }




}

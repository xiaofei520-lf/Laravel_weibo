<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Status extends Model
{
    use HasFactory;
    //反向  属于 当前的微博属于那个用户 默认的外键是 user_id
    public function user(){
        return $this->belongsTo(User::class);
    }
    protected $fillable = ['content'];

}

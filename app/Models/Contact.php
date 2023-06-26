<?php

namespace App\Models;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = ["name","phone","email","address","user_id"];
    protected $with = ['galleries'];

    public function galleries(){
        return $this->hasMany(Gallery::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}

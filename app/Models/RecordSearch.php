<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordSearch extends Model
{
    use HasFactory;

    protected $fillable = ["record","user_id"];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Managers extends Model
{
    use HasFactory;
	  protected $fillable = [
        'user_id',
        'parent_user_id',
    ];
}

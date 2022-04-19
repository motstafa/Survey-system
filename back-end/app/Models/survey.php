<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class survey extends Model
{

    
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = ["id_user","date","videoGames","TV","Sport","questionsAnswerd"];
    public $timestamps = false;  
    protected $table ="survey";
}

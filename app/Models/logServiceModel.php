<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class logServiceModel extends Model
{
    use HasFactory;
    protected $table = 'log_service';
    protected $guarded = [];
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    public $table = 'tbl_partner';
    public $timestamps = false;
    public $incrementing = false;
    
    protected $fillable = [
        
    ];
}

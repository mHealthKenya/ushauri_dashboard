<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;
    public $table = 'tbl_master_facility';
    public $timestamps = false;
    public $incrementing = false;
    
    protected $fillable = [
        
    ];
}

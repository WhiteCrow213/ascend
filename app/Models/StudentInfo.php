<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfo extends Model
{
    protected $table = 'tbl_student_info';
    protected $primaryKey = 'studID';
    public $timestamps = false;

    // If studID is AUTO-INCREMENT INT in phpMyAdmin, keep these:
    public $incrementing = true;
    protected $keyType = 'int';

    protected $guarded = [];
}

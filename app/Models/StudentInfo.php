<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentInfo extends Model
{
    // Database: student, Table: info
    protected $table = 'student.info';

    protected $primaryKey = 'id';

    // If your table does NOT have created_at and updated_at columns
    public $timestamps = false;

    protected $guarded = [];
}

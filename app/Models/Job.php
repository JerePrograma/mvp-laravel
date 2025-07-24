<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $table = 'jobs';
    public $timestamps = false; // la tabla no incluye timestamps estándar
}

class JobBatch extends Model
{
    protected $table = 'job_batches';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = false; // la clave primaria es string
}

class FailedJob extends Model
{
    protected $table = 'failed_jobs';
    public $timestamps = false;
}

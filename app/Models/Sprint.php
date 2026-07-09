<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Sprint.php
class Sprint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'name', 'description',
        'start_date', 'end_date', 'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

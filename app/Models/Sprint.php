<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model
{
        protected $fillable = [
        "name","description","status"
    ];

    protected $guarded = [
        "id","start_date","end_date"
    ];

    protected $hidden = [
         "created_at","updated_at"
    ];

    public function project() {
    return $this->belongsTo(Project::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }
}

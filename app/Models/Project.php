<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
protected $fillable = [
    'name', 'description', 'status', 'start_date', 'end_date', 'created_by', 'user_id'
];

public function users() {
    return $this->belongsToMany(User::class, 'project_user');
}

//     public function user() {
//         return $this->belongsTo(User::class);
//     }
public function creator() {
    return $this->belongsTo(User::class, 'user_id');
}
    public function sprints() {
        return $this->hasMany(Sprint::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'assigned_to',
        'sprint_id' 
    ];
    public function sprint() {
    return $this->belongsTo(Sprint::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
    public function assignedUser()
{
    return $this->belongsTo(User::class, 'assigned_to');
}
}

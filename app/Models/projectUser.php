<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class projectUser extends Model
{
   public function projects()
{
    return $this->belongsToMany(Project::class);
}

public function users()
{
    return $this->belongsToMany(User::class);
}
}

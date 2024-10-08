<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'last_activity', 'num-of-hours');
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id');
    }
    

    public function lastTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }
    public function oldTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }

    public function heightPriority()
    {
        return $this->hasOne(Task::class)
        ->ofMany([
            'priority' => 'max', 
        ],function($q){
            
            $q->where('priority', 'height') 
            ->where('title', 'like', 'A%'); 
        });
    }
}

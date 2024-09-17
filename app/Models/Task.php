<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'due_date',
        'deadline',
        'status',
        'assigned_to',
        'owner_id',
        'project_id',
        'start_time',
        'end_time',
        'note'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'short_name'];

    public function timetables(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

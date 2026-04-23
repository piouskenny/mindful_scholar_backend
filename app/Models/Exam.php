<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_code',
        'course_name',
        'exam_date',
        'venue',
    ];

    protected $casts = [
        'exam_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the number of days remaining until the exam.
     */
    public function getDaysLeftAttribute(): int
    {
        return max(0, (int) now()->startOfDay()->diffInDays($this->exam_date->startOfDay(), false));
    }

    /**
     * Get the urgency label based on days remaining.
     */
    public function getUrgencyAttribute(): string
    {
        $days = $this->days_left;
        if ($days <= 5) return 'Urgent';
        if ($days <= 10) return 'Soon';
        return 'Upcoming';
    }
}

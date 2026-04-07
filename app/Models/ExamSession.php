<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExamSession extends Model {
    protected $fillable = ['user_id', 'exam_id', 'started_at', 'submitted_at', 'score', 'status'];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function exam() {
        return $this->belongsTo(Exam::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function answers() {
        return $this->hasMany(Answer::class);
    }
    public function violations() {
        return $this->hasMany(Violation::class);
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Question extends Model {
    protected $fillable = ['exam_id', 'question_text', 'marks'];

    public function exam() {
        return $this->belongsTo(Exam::class);
    }
    public function options() {
        return $this->hasMany(Option::class);
    }
    public function correctOption() {
        return $this->hasOne(Option::class)->where('is_correct', true);
    }
}
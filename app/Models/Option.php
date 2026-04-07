<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['question_id', 'option_text', 'is_correct'];

    /**
     * FIX: PostgreSQL returns is_correct as string "1"/"0" or "t"/"f".
     * Casting to boolean ensures it works correctly in both SQLite and PostgreSQL.
     */
    protected $casts = [
    'is_correct' => 'integer',
];

    public function question() {
        return $this->belongsTo(Question::class);
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Violation extends Model {
    protected $fillable = ['exam_session_id', 'type', 'occurred_at'];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];
}
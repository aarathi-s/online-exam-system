<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->timestamp('occurred_at');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('violations');
    }
};
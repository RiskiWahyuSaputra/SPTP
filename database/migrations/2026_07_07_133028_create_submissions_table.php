<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('submission_number', 30)->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->date('submission_date');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('current_status', 30)->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index('current_status');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};

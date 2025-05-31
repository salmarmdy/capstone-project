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
         Schema::create('assessment_checklist_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->unsignedBigInteger('checklist_items_id');
            $table->boolean('passed')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('assessment_id')
                  ->references('id')
                  ->on('assessments')
                  ->onDelete('cascade');
                  
            $table->foreign('checklist_items_id')
                  ->references('id')
                  ->on('checklist_items')
                  ->onDelete('cascade');

            // Unique constraint untuk mencegah duplikasi
            $table->unique(['assessment_id', 'checklist_items_id'], 'unique_assessment_checklist');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_checklist_results');
    }
};

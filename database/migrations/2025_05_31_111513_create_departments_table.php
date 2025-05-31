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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Kode departemen');
            $table->string('name')->unique()->comment('Nama departemen');
            $table->text('description')->nullable()->comment('Deskripsi departemen');
            $table->boolean('status')->default(true)->comment('Status aktif/nonaktif');
            $table->timestamps();
        });

        // Insert default departments
        DB::table('departments')->insert([
            [
                'code' => 'IT',
                'name' => 'IT',
                'description' => 'Information Technology Department',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FIN',
                'name' => 'Finance',
                'description' => 'Finance Department',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'HR',
                'name' => 'HR',
                'description' => 'Human Resources Department',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MKT',
                'name' => 'Marketing',
                'description' => 'Marketing Department',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OPS',
                'name' => 'Operations',
                'description' => 'Operations Department',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

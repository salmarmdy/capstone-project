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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('type', ['sim', 'stnk']); // Jenis notifikasi
            $table->string('document_number'); // Nomor SIM/STNK
            $table->date('expiry_date'); // Tanggal kadaluarsa
            $table->string('title'); // Judul notifikasi
            $table->text('message'); // Isi pesan
            $table->boolean('is_read')->default(false); // Status dibaca
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

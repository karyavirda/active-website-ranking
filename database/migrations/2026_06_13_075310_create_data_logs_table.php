<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data_logs', function (Blueprint $table) {
            $table->id();
            $table->string('subdomain');
            $table->string('nama_admin');
            $table->string('aktivitas');
            $table->timestamp('activity_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_logs');
    }
};

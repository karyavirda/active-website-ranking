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
        Schema::table('kriterias', function (Blueprint $table) {
            $table->string('satuan')->nullable()->after('bobot');
            $table->text('deskripsi')->nullable()->after('satuan');
        });
    }

    public function down(): void
    {
        Schema::table('kriterias', function (Blueprint $table) {
            $table->dropColumn(['satuan', 'deskripsi']);
        });
    }
};

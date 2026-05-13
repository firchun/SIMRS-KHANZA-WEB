<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_poli', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 100);
            $table->decimal('biaya', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('master_kamar', function (Blueprint $table) {
            $table->id();
            $table->string('bangsal', 50);
            $table->string('kelas', 20);
            $table->string('no_kamar', 20);
            $table->string('status', 20)->default('tersedia');
            $table->timestamps();
        });

        Schema::create('master_tindakan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique();
            $table->string('nama', 200);
            $table->decimal('biaya', 15, 2)->default(0);
            $table->string('kategori', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_tindakan');
        Schema::dropIfExists('master_kamar');
        Schema::dropIfExists('master_poli');
    }
};

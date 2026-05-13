<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->string('no_rm', 20)->unique();
            $table->string('nama', 100);
            $table->string('nik', 30)->nullable()->unique();
            $table->string('tempat_lahir', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('kelurahan', 50)->nullable();
            $table->string('kecamatan', 50)->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('provinsi', 50)->nullable();
            $table->string('no_telp', 20)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('gol_darah', 5)->nullable();
            $table->string('pekerjaan', 50)->nullable();
            $table->string('agama', 20)->nullable();
            $table->string('pendidikan', 20)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('status_nikah', 20)->nullable();
            $table->string('penanggungjawab', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('registrasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien');
            $table->string('no_reg', 30)->unique();
            $table->dateTime('tgl_registrasi');
            $table->enum('jenis', ['IGD', 'RALAN', 'RANAP']);
            $table->string('poli', 50)->nullable();
            $table->string('dokter', 100)->nullable();
            $table->string('cara_bayar', 50)->nullable();
            $table->string('no_asuransi', 50)->nullable();
            $table->string('penjamin', 100)->nullable();
            $table->string('status', 20)->default('antri');
            $table->text('keluhan')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('igd_triage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasi');
            $table->string('triase', 20);
            $table->string('tekanan_darah', 20)->nullable();
            $table->string('nadi', 20)->nullable();
            $table->string('suhu', 10)->nullable();
            $table->string('pernapasan', 20)->nullable();
            $table->string('spo2', 10)->nullable();
            $table->string('kesadaran', 30)->nullable();
            $table->string('nyeri', 10)->nullable();
            $table->text('anamnesis')->nullable();
            $table->foreignId('dokter_id')->nullable()->constrained('users');
            $table->dateTime('tgl_triase');
            $table->timestamps();
        });

        Schema::create('igd_tindakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasi');
            $table->string('tindakan', 200);
            $table->integer('jumlah')->default(1);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('tgl_tindakan');
            $table->timestamps();
        });

        Schema::create('igd_diagnosis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasi');
            $table->string('kode_icd', 20)->nullable();
            $table->string('diagnosis', 255);
            $table->enum('jenis', ['utama', 'sekunder', 'komplikasi']);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('ralan_kunjungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasi');
            $table->string('no_antrian', 10);
            $table->string('poli', 50);
            $table->string('dokter', 100)->nullable();
            $table->string('status', 20)->default('menunggu');
            $table->dateTime('tgl_kunjungan');
            $table->string('tekanan_darah', 20)->nullable();
            $table->string('nadi', 20)->nullable();
            $table->string('suhu', 10)->nullable();
            $table->string('berat_badan', 10)->nullable();
            $table->string('tinggi_badan', 10)->nullable();
            $table->text('keluhan')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('tindakan')->nullable();
            $table->text('resep')->nullable();
            $table->foreignId('dokter_id')->nullable()->constrained('users');
            $table->dateTime('tgl_pemeriksaan')->nullable();
            $table->string('status_pulang', 30)->nullable();
            $table->timestamps();
        });

        Schema::create('ralan_resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('ralan_kunjungan');
            $table->string('obat', 200);
            $table->string('satuan', 20)->nullable();
            $table->integer('jumlah');
            $table->integer('jumlah_diberikan')->nullable();
            $table->string('aturan_pakai', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('ranap_admisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registrasi_id')->constrained('registrasi');
            $table->string('no_kamar', 20);
            $table->string('kelas', 20);
            $table->string('bangsal', 50);
            $table->string('diagnosis_masuk', 255);
            $table->string('dokter_merawat', 100);
            $table->dateTime('tgl_masuk');
            $table->string('cara_masuk', 50)->nullable();
            $table->text('catatan_masuk')->nullable();
            $table->dateTime('tgl_keluar')->nullable();
            $table->string('diagnosis_keluar', 255)->nullable();
            $table->string('status_pulang', 30)->nullable();
            $table->string('keadaan_keluar', 50)->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('ranap_tindakan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admisi_id')->constrained('ranap_admisi');
            $table->string('tindakan', 200);
            $table->integer('jumlah')->default(1);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->dateTime('tgl_tindakan');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('ranap_catatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admisi_id')->constrained('ranap_admisi');
            $table->text('catatan');
            $table->string('jenis', 50);
            $table->dateTime('tgl_catatan');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });

        Schema::create('ranap_visite', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admisi_id')->constrained('ranap_admisi');
            $table->string('dokter', 100);
            $table->dateTime('tgl_visite');
            $table->text('hasil_pemeriksaan')->nullable();
            $table->text('instruksi')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 30)->nullable()->after('email');
            $table->string('role', 30)->default('operator')->after('nik');
            $table->string('spesialisasi', 100)->nullable()->after('role');
            $table->string('no_str', 50)->nullable()->after('spesialisasi');
            $table->boolean('is_active')->default(true)->after('no_str');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranap_visite');
        Schema::dropIfExists('ranap_catatan');
        Schema::dropIfExists('ranap_tindakan');
        Schema::dropIfExists('ranap_admisi');
        Schema::dropIfExists('ralan_resep');
        Schema::dropIfExists('ralan_kunjungan');
        Schema::dropIfExists('igd_diagnosis');
        Schema::dropIfExists('igd_tindakan');
        Schema::dropIfExists('igd_triage');
        Schema::dropIfExists('registrasi');
        Schema::dropIfExists('pasien');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nik', 'role', 'spesialisasi', 'no_str', 'is_active']);
        });
    }
};

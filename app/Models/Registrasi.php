<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Registrasi extends Model
{
    protected $table = 'registrasi';
    protected $fillable = ['pasien_id', 'no_reg', 'tgl_registrasi', 'jenis', 'poli', 'dokter', 'cara_bayar', 'no_asuransi', 'penjamin', 'status', 'keluhan', 'user_id'];
    protected $casts = ['tgl_registrasi' => 'datetime'];

    public function pasien() { return $this->belongsTo(Pasien::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function igdTriage() { return $this->hasOne(IgdTriage::class); }
    public function igdTindakan() { return $this->hasMany(IgdTindakan::class); }
    public function igdDiagnosis() { return $this->hasMany(IgdDiagnosis::class); }
    public function ralanKunjungan() { return $this->hasOne(RalanKunjungan::class); }
    public function ranapAdmisi() { return $this->hasOne(RanapAdmisi::class); }

    public static function generateNoReg($jenis) {
        $prefix = match($jenis) { 'IGD' => 'IGD', 'RALAN' => 'RL', 'RANAP' => 'RN', default => 'RG' };
        return $prefix . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
    }
}

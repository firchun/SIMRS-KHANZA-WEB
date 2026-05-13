<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RalanKunjungan extends Model
{
    protected $table = 'ralan_kunjungan';
    protected $fillable = ['registrasi_id', 'no_antrian', 'poli', 'dokter', 'status', 'tgl_kunjungan', 'tekanan_darah', 'nadi', 'suhu', 'berat_badan', 'tinggi_badan', 'keluhan', 'diagnosis', 'tindakan', 'resep', 'dokter_id', 'tgl_pemeriksaan', 'status_pulang'];
    protected $casts = ['tgl_kunjungan' => 'datetime', 'tgl_pemeriksaan' => 'datetime'];

    public function registrasi() { return $this->belongsTo(Registrasi::class); }
    public function resep() { return $this->hasMany(RalanResep::class, 'kunjungan_id'); }
}

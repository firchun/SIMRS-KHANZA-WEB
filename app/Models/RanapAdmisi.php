<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RanapAdmisi extends Model
{
    protected $table = 'ranap_admisi';
    protected $fillable = ['registrasi_id', 'no_kamar', 'kelas', 'bangsal', 'diagnosis_masuk', 'dokter_merawat', 'tgl_masuk', 'cara_masuk', 'catatan_masuk', 'tgl_keluar', 'diagnosis_keluar', 'status_pulang', 'keadaan_keluar', 'user_id'];
    protected $casts = ['tgl_masuk' => 'datetime', 'tgl_keluar' => 'datetime'];

    public function registrasi() { return $this->belongsTo(Registrasi::class); }
    public function tindakan() { return $this->hasMany(RanapTindakan::class, 'admisi_id'); }
    public function catatan() { return $this->hasMany(RanapCatatan::class, 'admisi_id'); }
    public function visite() { return $this->hasMany(RanapVisite::class, 'admisi_id'); }
}

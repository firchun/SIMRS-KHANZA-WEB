<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RanapVisite extends Model
{
    protected $table = 'ranap_visite';
    protected $fillable = ['admisi_id', 'dokter', 'tgl_visite', 'hasil_pemeriksaan', 'instruksi'];
    protected $casts = ['tgl_visite' => 'datetime'];
    public function admisi() { return $this->belongsTo(RanapAdmisi::class, 'admisi_id'); }
}

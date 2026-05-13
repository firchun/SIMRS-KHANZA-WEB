<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IgdTriage extends Model
{
    protected $table = 'igd_triage';
    protected $fillable = ['registrasi_id', 'triase', 'tekanan_darah', 'nadi', 'suhu', 'pernapasan', 'spo2', 'kesadaran', 'nyeri', 'anamnesis', 'dokter_id', 'tgl_triase'];
    protected $casts = ['tgl_triase' => 'datetime'];

    public function registrasi() { return $this->belongsTo(Registrasi::class); }
}

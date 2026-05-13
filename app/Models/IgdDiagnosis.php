<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IgdDiagnosis extends Model
{
    protected $table = 'igd_diagnosis';
    protected $fillable = ['registrasi_id', 'kode_icd', 'diagnosis', 'jenis', 'user_id'];
    public function registrasi() { return $this->belongsTo(Registrasi::class); }
}

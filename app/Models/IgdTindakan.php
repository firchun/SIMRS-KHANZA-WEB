<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IgdTindakan extends Model
{
    protected $table = 'igd_tindakan';
    protected $fillable = ['registrasi_id', 'tindakan', 'jumlah', 'tarif', 'user_id', 'tgl_tindakan'];
    protected $casts = ['tgl_tindakan' => 'datetime', 'tarif' => 'decimal:2'];

    public function registrasi() { return $this->belongsTo(Registrasi::class); }
}

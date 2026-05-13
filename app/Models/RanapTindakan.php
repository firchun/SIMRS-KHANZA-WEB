<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RanapTindakan extends Model
{
    protected $table = 'ranap_tindakan';
    protected $fillable = ['admisi_id', 'tindakan', 'jumlah', 'tarif', 'tgl_tindakan', 'user_id'];
    protected $casts = ['tgl_tindakan' => 'datetime'];
    public function admisi() { return $this->belongsTo(RanapAdmisi::class, 'admisi_id'); }
}

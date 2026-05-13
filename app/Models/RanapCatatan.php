<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RanapCatatan extends Model
{
    protected $table = 'ranap_catatan';
    protected $fillable = ['admisi_id', 'catatan', 'jenis', 'tgl_catatan', 'user_id'];
    protected $casts = ['tgl_catatan' => 'datetime'];
    public function admisi() { return $this->belongsTo(RanapAdmisi::class, 'admisi_id'); }
}

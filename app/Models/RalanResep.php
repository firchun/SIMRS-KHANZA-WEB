<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RalanResep extends Model
{
    protected $table = 'ralan_resep';
    protected $fillable = ['kunjungan_id', 'obat', 'satuan', 'jumlah', 'jumlah_diberikan', 'aturan_pakai', 'catatan'];
    public function kunjungan() { return $this->belongsTo(RalanKunjungan::class, 'kunjungan_id'); }
}

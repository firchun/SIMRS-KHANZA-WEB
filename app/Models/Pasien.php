<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pasien extends Model
{
    use SoftDeletes;
    protected $table = 'pasien';
    protected $fillable = ['no_rm', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'rt', 'rw', 'kelurahan', 'kecamatan', 'kota', 'provinsi', 'no_telp', 'no_hp', 'gol_darah', 'pekerjaan', 'agama', 'pendidikan', 'status_nikah', 'penanggungjawab', 'catatan'];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function registrasi() { return $this->hasMany(Registrasi::class); }
    public static function generateNoRM() { return 'RM-' . date('Ymd') . '-' . str_pad(static::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT); }
}

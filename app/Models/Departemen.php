<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    protected $guarded = [''];
    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function penilaian()
    {
        return $this->hasMany(Penilaian::class);
    }
}

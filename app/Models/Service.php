<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function forms()
    {
        return $this->hasMany(Form::class);
    }
}

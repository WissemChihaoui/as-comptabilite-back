<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'service_id', 'type'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
    public function userDocuments()
    {
        return $this->hasMany(UserDocument::class);
    }
}

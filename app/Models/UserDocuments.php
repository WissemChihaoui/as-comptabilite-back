<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDocuments extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'document_id', 'file_path', 'original_name', 'mime_type', 'file_size'];

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }
}

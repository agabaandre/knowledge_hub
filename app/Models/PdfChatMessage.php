<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfChatMessage extends Model
{
    use HasFactory;

    protected $fillable = ['pdf_chat_session_id', 'role', 'content'];

    public function session()
    {
        return $this->belongsTo(PdfChatSession::class, 'pdf_chat_session_id');
    }
}

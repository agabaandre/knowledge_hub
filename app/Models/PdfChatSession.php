<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdfChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'publication_id',
        'forum_id',
        'attachment_id',
        'pdf_selection_key',
        'chatpdf_meta',
        'source_id',
        'assistant_mode',
    ];

    protected $casts = [
        'chatpdf_meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function attachment()
    {
        return $this->belongsTo(PublicationAttachment::class, 'attachment_id');
    }

    public function messages()
    {
        return $this->hasMany(PdfChatMessage::class, 'pdf_chat_session_id')->orderBy('created_at');
    }
}

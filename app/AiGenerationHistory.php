<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AiGenerationHistory extends Model
{
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const FAILED = 'failed';
    
    const TYPE_CATEGORY = 'category';
    const TYPE_ANNOUNCEMENT = 'announcement';

    const SOURCE_PARSE_HTML = 'parse_html';
    const SOURCE_AI_DATABASE = 'ai_database';

    protected $fillable = [
        'user_id',
        'status',
        'parrameters',
        'type',
        'prompt',
        'result',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'parrameters' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

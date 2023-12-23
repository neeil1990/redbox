<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PolicyTermsDocs extends Model
{
    protected $table = 'policy_and_terms_docs';

    protected $guarded = [];

    public static function editDocument($name, $content)
    {
        $document = self::first();
        if (!isset($document)) {
            $document = new self();
        }
        $document[$name] = $content;
        $document['last_editor'] = Auth::id();

        $document->save();
    }
}

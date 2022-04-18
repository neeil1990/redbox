<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RelevanceAnalyseResults extends Model
{
    protected $table = 'analyze_relevance';

    protected $guarded = [];

    public $timestamps = false;
}

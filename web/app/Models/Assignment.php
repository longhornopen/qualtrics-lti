<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_link_dbid',
        'qualtrics_url',
        'intro_text',
        'finish_text'
    ];
}

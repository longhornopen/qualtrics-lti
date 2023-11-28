<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 * @method static firstOrCreate(array $array, string[] $array1)
 * @method static where(string $string, mixed $get)
 * @property string share_data
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

    public function shouldSendPersonalData($type) : bool
    {
        if (!$this->share_data) {
            return false;
        }
        return in_array($type, explode(',', $this->share_data));
    }
}

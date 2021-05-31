<?php

namespace App\Models;

use ceLTIc\LTI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Model
 * @method static where(string $string, $id)
 * @method static firstOrCreate(array $array, array $array1)
 * @method static findOrFail(mixed $get)
 * @property string user_name
 * @property string user_email
 */
class AssignmentResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_result_id',
        'user_name',
        'user_email',
    ];

    protected $casts = [
        'date_outcome_reported' => 'datetime'
    ];

    public function getPersonIdentity(): string {
        if ($this->user_name && $this->user_email) {
            return $this->user_name . ' (' . $this->user_email . ')';
        }
        if ($this->user_name) {
            return $this->user_name;
        }
        return 'anonymous';
    }
}

<?php

namespace App\Models;

use ceLTIc\LTI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @mixin Builder
 */
class AssignmentResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_result_id'
    ];

    protected $casts = [
        'date_outcome_reported' => 'datetime'
    ];

    public function getPersonIdentity() {
        $lti_db_connector = LTI\DataConnector\DataConnector::getDataConnector(DB::connection()->getPdo());
        $user_result = LTI\UserResult::fromRecordId(
            $this->user_result_id,
            $lti_db_connector
        );
        if ($user_result->fullname) {
            return $user_result->fullname;
        }
        if ($user_result->email) {
            return $user_result->email;
        }
        if ($user_result->ltiUserId) {
            return $user_result->ltiUserId;
        }
    }
}

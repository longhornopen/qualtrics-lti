<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentResponse;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SurveyReturnUrlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        $this->artisan('migrate');

        // LTI resource links without a grade passback service, so returns only record the score locally
        $now = now();
        DB::table('lti2_consumer')->insert([
            'consumer_pk' => 1, 'name' => 'Test LMS', 'consumer_key' => 'test-key',
            'protected' => 0, 'enabled' => 1, 'created' => $now, 'updated' => $now,
        ]);
        foreach ([11, 12] as $resource_link_pk) {
            DB::table('lti2_resource_link')->insert([
                'resource_link_pk' => $resource_link_pk, 'consumer_pk' => 1,
                'lti_resource_link_id' => 'link-' . $resource_link_pk, 'settings' => '{}',
                'created' => $now, 'updated' => $now,
            ]);
        }

        Assignment::create([
            'resource_link_dbid' => 11,
            'qualtrics_url' => 'https://example.qualtrics.com/jfe/form/SV_abc',
            'intro_text' => '',
            'finish_text' => '',
        ]);
        Assignment::create([
            'resource_link_dbid' => 12,
            'qualtrics_url' => 'https://example.qualtrics.com/jfe/form/SV_abc',
            'intro_text' => '',
            'finish_text' => '',
        ]);
    }

    private function studentSession($resource_link_dbid = 11, $user_result_dbid = 22)
    {
        return [
            'lti_is_teacher' => false,
            'lti_resource_link_dbid' => $resource_link_dbid,
            'lti_user_result_dbid' => $user_result_dbid,
            'lti_user_name' => 'A. Student',
            'lti_user_email' => 'student',
        ];
    }

    private function surveyUrlForLaunch($uuid, $session_data)
    {
        $response = $this->withSession(['lti_session_exists' => true, 'uuid-' . $uuid => $session_data])
            ->get('/app/' . $uuid);
        $response->assertOk();
        return $response->viewData('full_qualtrics_url');
    }

    private function returnPath($survey_url)
    {
        parse_str(parse_url($survey_url, PHP_URL_QUERY), $query);
        return parse_url($query['return_url'], PHP_URL_PATH);
    }

    public function testSurveyUrlIsTheSameOnEveryLaunch()
    {
        $first = $this->surveyUrlForLaunch('0dbb87e3-c859-47fe-a64f-1efc16a3e1b3', $this->studentSession());
        $second = $this->surveyUrlForLaunch('54012d3c-6b58-410f-aff2-c796a65d8cbb', $this->studentSession());

        $this->assertSame($first, $second);
        $this->assertStringNotContainsString('0dbb87e3-c859-47fe-a64f-1efc16a3e1b3', $first);
    }

    public function testSurveyUrlDiffersPerStudentAndPerAssignment()
    {
        $base = $this->surveyUrlForLaunch('00000000-0000-4000-8000-000000000001', $this->studentSession(11, 22));
        $other_student = $this->surveyUrlForLaunch('00000000-0000-4000-8000-000000000002', $this->studentSession(11, 23));
        $other_assignment = $this->surveyUrlForLaunch('00000000-0000-4000-8000-000000000003', $this->studentSession(12, 22));

        $this->assertNotSame($base, $other_student);
        $this->assertNotSame($base, $other_assignment);
    }

    public function testReturnRecordsScoreAgainstTheLatestLaunch()
    {
        $old_uuid = '00000000-0000-4000-8000-00000000000a';
        $new_uuid = '00000000-0000-4000-8000-00000000000b';
        $return_path = $this->returnPath($this->surveyUrlForLaunch($old_uuid, $this->studentSession()));

        $response = $this->withSession([
            'lti_session_exists' => true,
            'uuid-' . $old_uuid => $this->studentSession(),
            'uuid-' . $new_uuid => $this->studentSession(),
        ])->get($return_path . '?Score=3&MaxScore=4');

        $response->assertRedirect('/app/' . $new_uuid);
        $this->assertEquals(0.75, AssignmentResponse::where('user_result_id', 22)->firstOrFail()->score);
    }

    public function testReturnWithoutAMatchingLaunchIsRejected()
    {
        $return_path = $this->returnPath(
            $this->surveyUrlForLaunch('00000000-0000-4000-8000-000000000001', $this->studentSession())
        );

        $this->flushSession();

        // a different student's launch in this browser session must not pick up the score
        $response = $this->withSession([
            'lti_session_exists' => true,
            'uuid-00000000-0000-4000-8000-000000000002' => $this->studentSession(11, 23),
        ])->get($return_path . '?Score=4&MaxScore=4');
        $response->assertOk();
        $response->assertViewIs('error');

        $response = $this->withSession(['lti_session_exists' => true])
            ->get('/survey_return/not-a-real-token?Score=4&MaxScore=4');
        $response->assertOk();
        $response->assertViewIs('error');

        $this->assertNull(AssignmentResponse::where('user_result_id', 22)->firstOrFail()->score);
    }

    public function testReturnWithoutAnLtiSessionIsRejected()
    {
        $return_path = $this->returnPath(
            $this->surveyUrlForLaunch('00000000-0000-4000-8000-000000000001', $this->studentSession())
        );
        $this->flushSession();

        $this->get($return_path . '?Score=4&MaxScore=4')
            ->assertSee('Sorry, your session has expired.');
        $this->assertNull(AssignmentResponse::where('user_result_id', 22)->firstOrFail()->score);
    }

    public function testLegacyPerLaunchReturnUrlStillWorks()
    {
        // surveys started before this change carry the old /app/{uuid}/response return URL
        $uuid = '00000000-0000-4000-8000-000000000001';
        $this->surveyUrlForLaunch($uuid, $this->studentSession());

        $this->withSession(['lti_session_exists' => true, 'uuid-' . $uuid => $this->studentSession()])
            ->get('/app/' . $uuid . '/response?Score=1&MaxScore=2')
            ->assertRedirect('/app/' . $uuid);
        $this->assertEquals(0.5, AssignmentResponse::where('user_result_id', 22)->firstOrFail()->score);
    }
}

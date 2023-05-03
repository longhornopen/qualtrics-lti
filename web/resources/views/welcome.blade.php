<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Qualtrics LTI Tool</title>

    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
    <link rel="stylesheet" href="/custom/custom.css">

    <script src="{{ asset('/js/manifest.js') }}"></script>
    <script src="{{ asset('/js/vendor.js') }}"></script>
    <script src="{{ asset('/js/app.js') }}"></script>
</head>
<body class="page_home">

<div class="container">

    <div class="row header" id="header_row">
        <div class="col-md-12">
            <h3>Qualtrics LTI Tool</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card card_instructions">
                <div class="card-body">
                    <h4 class="card-title">What is this?</h4>
                    <p class="card-text">
                        Qualtrics is a survey and quiz tool.
                    </p>
                    <p class="card-text">
                        This tool allows you to create a survey or quiz in Qualtrics and offer it to your students through Canvas as a graded activity.  Grades can be either completion grades, or a grade calculated by Qualtrics.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card_instructions">
                <div class="card-body">
                    <h4 class="card-title">How do I create a Qualtrics survey?</h4>
                    <p class="card-text">
                        If you already know how to set up a survey or quiz in Qualtrics, great!
                        There's only a few additional steps to link it to Canvas; skip to the next item to find out how.
                    </p>
                    <p class="card-text">
                        If you need help setting up a quiz in Qualtrics, login to Qualtrics then click the 'Help' link in the upper right.  Your institution may provide additional Qualtrics support.
                    </p>
                    <p  class="card-text" style="font-style:italic;">
                        Also see 'How do I grade a Qualtrics survey?' to learn how to set up a survey for grading.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card_instructions">
                <div class="card-body">
                    <h4 class="card-title">How do I attach a Qualtrics survey to a course?</h4>
                    <p class="card-text">
                        Your institution has made this tool available in your LMS or course-management system.  You can add placements of this tool to your course there, and then link them to individual Qualtrics surveys.
                    </p>
                    <p class="card-text" style="font-style:italic;">
                        Also see 'How do I grade a Qualtrics survey?' to learn how to set up a survey for grading.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card card_instructions">
                <div class="card-body">
                    <h4 class="card-title">How do I grade a Qualtrics survey?</h4>
                    <p class="card-text">
                        If you want a participation score (where every student gets 100%),
                    <ol>
                        <li>Edit your survey and click 'Survey Flow'.</li>
                        <li>Add a new 'End of Survey' element</li>
                        <li>Click 'Customize'</li>
                        <li>Select 'Redirect to a URL'.</li>
                        <li>For the URL, enter <pre>${e://Field/return_url}?Score=100&MaxScore=100</pre></li>
                    </ol>
                    </p>
                    <p class="card-text">
                        If you want to calculate a grade in Qualtrics,*
                    <ol>
                        <li><a href="https://www.qualtrics.com/support/survey-platform/survey-module/survey-tools/response-management-tools/scoring/" target="_blank">Set up a Scoring category in Qualtrics</a> and score your questions.</li>
                        <li>Edit your survey and click 'Survey Flow'.</li>
                        <li>Click 'Add a New Element Here'.</li>
                        <li>Choose an 'Embedded Data' element.</li>
                        <li>When asked for an 'Embedded Data Field Name', enter 'score' (no quotes, all lowercase).</li>
                        <li>Click 'Set a Value Now'.</li>
                        <li>Choose 'Insert Piped Text' > 'Scoring' and select your scoring category.</li>
                        <li>Still in the 'Survey Flow' screen, click add a new 'End of Survey' element.</li>
                        <li>Click 'Customize'.</li>
                        <li>Select 'Redirect to a URL'.  For the URL, enter <pre>${e://Field/return_url}?Score=${e://Field/score}&MaxScore=100</pre></li>
                    </ol>
                    *The above assumes that a perfect score is 100 points.  If your survey is different, change the '100' on the end of the URL to your maximum possible score.
                    </p>
                </div>
            </div>
        </div>

    </div><!-- Row -->

    @if (!env('APP_HIDE_LONGHORNOPEN_BRANDING'))
    <div class="row footer" id="footer_row">
        <div class="float-right" id="branding_logo">Provided by <a id="provided_link" href="https://longhornopen.github.io/"><img height="40" src="/img/longhorn_open_logo.svg"> Longhorn Open</a></div>
    </div>
    @endif
</div>



</body>
</html>

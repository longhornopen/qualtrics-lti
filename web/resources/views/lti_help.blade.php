<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Qualtrics LTI Tool</title>

    <link rel="stylesheet" href="{{ asset('/css/app.css') }}">
</head>
<body>

<div class="container">
    <h1>Qualtrics LTI Tool installation help</h1>
    <p>
        If you're seeing this, you've successfully installed the Qualtrics LTI tool.  Here's some information
        about how to integrate it into your LMS.
        <br><br>
        If you need some LMS-specific info that's not covered here, it may be described at
        <a href="https://github.com/longhornopen/laravel-celtic-lti/wiki/LTI-Key-Generation" target="_blank">https://github.com/longhornopen/laravel-celtic-lti/wiki/LTI-Key-Generation</a>
    </p>
    <hr>
    <h2>LTI 1.3</h2>
    <p>
        LTI 1.3 is the preferred method to register this tool with your LMS.
    </p>
    <div>
    <p>
        ➔ First, set up your RSA public and private keys.
    </p>
    @if (env('LTI13_RSA_PUBLIC_KEY') && env('LTI13_RSA_PRIVATE_KEY'))
        <p style="border:solid 1px black; margin-left:20px; padding:10px;">
            <span style="font-weight:bold;color:green;font-size:120%">✓</span> Congratulations, looks like you already did this!
        </p>
    @else
        <p style="border:solid 1px black; margin-left:20px; padding:10px;">
            The file at <code>config/lti.php</code> contains the RSA info you need to provide.  By default, they're read
            from environment variables, which is preferred, but in a pinch you can hard-code values there.
            <br><br>
            Several web-based apps such as <a href="https://cryptotools.net/rsagen">https://cryptotools.net/rsagen</a>
            can generate RSA keys for you, or you can do it from the command line.
        </p>
    @endif
    </div>
    <div>
    <p>
        ➔ Register this tool with your LMS.  Instructions on how to do this are LMS-specific.
    </p>
        <ul>
            <li>
                Canvas LMS
                <ul>
                    <li><a href="https://community.canvaslms.com/t5/Admin-Guide/How-do-I-configure-an-LTI-key-for-an-account/ta-p/140" target="_blank">Add a new Developer Key</a>.</li>
                    <li>Under 'Method', choose 'Manual Entry'.
                        <ul>
                            <li>For 'JWK Method', choose 'Public JWKS URL', with the value <code>{{env('APP_URL')}}/lti/jwks</code></li>
                            <li>For all other URLs, use <code>{{env('APP_URL')}}/lti</code></li>
                            <li>Under 'LTI Advantage Services', turn on "Can view submission data for assignments associated with the tool." and "Can create and update submission results for assignments associated with the tool."</li>
                            <li>Under 'Additional Settings', choose Privacy Level = 'Public'.</li>
                            <li>Under 'Placements', choose 'Assignment Selection'.</li>
                        </ul>
                    </li>
                    <li>Note the Client ID that was created.</li>
                    <li><a href="https://community.canvaslms.com/t5/Admin-Guide/How-do-I-configure-an-external-app-for-an-account-using-a-client/ta-p/202" target="_blank">Install the app into a course.</a></li>
                    <li>Note the Deployment ID that was created.  <i>(In the course's Settings menu, it's found in 'Apps' > 'View App Configurations' > 'Deployment ID' in the pulldown menu next to the app.)</i></li>
                </ul>
            </li>
            <li>
                Other LMSes
                <ul>
                    <li>
                        We don't currently have detailed instructions for other LMSes.  (Please contact us if you
                        can help write them!)  You'll need to set up the following, though:
                        <ul>
                            <li>JWKS URL: <code>{{env('APP_URL')}}/lti/jwks</code></li>
                            <li>All other URLs: <code>{{env('APP_URL')}}/lti</code></li>
                            <li>The Privacy level will need to allow reading student's names and submitting grades</li>
                            <li>LTI Advantage Services: the service to return grades to the LMS will need to be active</li>
                            <li>You'll need to get the app's Client ID, and the Deployment ID from an instance of the tool that you've deployed into a course.</li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div>
        <p>
            ➔ Register your LMS with this tool.
        </p>
        <ul>
            <li>
                In a command line, <code>cd</code> to the top-level folder of the web app - the one containing the file `artisan`.
            </li>
            <li>
                Type <code>php artisan lti:add_platform_1.3</code>.  You'll get a walkthrough of further commands to run,
                which will be LMS-specific.  For all of them, you'll need the Client ID and Deployment ID you noted above.
                <ul>
                    <li>For instance, to install into Canvas: <code>php artisan lti:add_platform_1.3 canvas-cloud --client_id=12345 --deployment_id=XYZ</code></li>
                </ul>
            </li>
        </ul>
    </div>
    <hr>
    <h2>LTI 1.0-1.2</h2>
    <p>
        Qualtrics LTI still works with older versions of LTI, but those versions aren't recommended anymore.  Use the LTI 1.3 instructions above if you have a choice.
    </p>
    <ul>
        <li><code>cd web</code></li>
        <li><code>php artisan lti:add_platform_1.2 my_lms_name my_consumer_key my_shared_secret</code></li>
        <li>Install into LMS with launch URL: <code>{{env('APP_URL')}}/lti</code> and the consumer key/secret you created above.</li>
    </ul>
</div>

</body>
</html>

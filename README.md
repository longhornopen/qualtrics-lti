# qualtrics-lti
A simple LTI tool for integrating Qualtrics surveys into a course.  Surveys can optionally be graded.

## Installation
* Clone or download this repo.
* Install PHP (7.4 or higher)
* Install Composer (https://getcomposer.org)
* `cd` into the 'web' folder in this repo.
* `cp .env.example .env` to create a new env file from the provided example.
* `composer install` (to install PHP dependencies that aren't included here)
* `php artisan key:generate` (to set your app's encryption key)
* Edit the '.env' file with information about your local environment.  (You can also provide these as environment variables instead, if you wish.)
* `php artisan migrate` (to create your database schema)
* Start the server.  See the 'Getting Started' section of the Laravel docs (https://laravel.com/docs/8.x/installation) for full details, but there are three easy ways to get started:
    * `php artisan serve` on the command line, which starts a server at http://localhost:8000
    * Install this into an Apache/PHP server, with the 'web/public/' folder as the webroot
    * Use the included Dockerfile to run this in a Docker setup.

## Updating

When updating to a new version:
* `cd web`, then run `php artisan migrate` to pick up any database schema changes.

## LMS Setup
Full docs about how to generate the info you need to install this tool in an LMS are at https://github.com/longhornopen/laravel-celtic-lti/wiki/LTI-Key-Generation

But for most users, something like this will suffice:

### LTI 1.2
* `cd web`
* `php artisan lti:add_platform_1.2 my_lms_name my_consumer_key my_shared_secret`
* Install into LMS with launch URL: `{YOUR_SERVER_URL}/lti` and the consumer key/secret you created above.

### LTI 1.3
Coming soon...

## Usage
When placed in a course, the Qualtrics LTI tool can be configured with the URL of a Qualtrics survey.  Students will be sent to this survey, and Qualtrics will return them to the LTI tool afterwards so that their grade can be recorded.  You'll have to add a survey step to the Qualtrics survey to make that happen.

Visit the home page on your installed server for full usage instructions.  You may want to use these instructions as a basis for your own institution-specific documentation, telling users where to find your Qualtrics instance, or how to install the tool in a course.

## Meta
This is a product of Longhorn Open Ed Tech, a group building open-source education tools housed at the University of Texas at Austin. See our homepage for more info about us and to discuss collaboration possibilities and ideas for new development.

Distributed under the Gnu Affero license. See LICENSE for more information.

## Contributing
We welcome bug reports and feature suggestions via the 'Issues' tab in Github.

If you'd like to contribute a feature or other change, we welcome pull requests. For new features or other large changes, please open an issue first and tell us what you're proposing.
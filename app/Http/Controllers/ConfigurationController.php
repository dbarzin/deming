<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    /*
    * Return the configuration
    */
    public function index()
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Get configuration
        $mail_from = config('deming.notification.mail-from');
        $mail_subject = config('deming.notification.mail-subject');
        $frequency = config('deming.notification.frequency');
        $expire_delay = config('deming.notification.expire-delay');
        $reminder = config('deming.notification.reminder');

        // Return
        return view(
            'config',
            compact('mail_from', 'mail_subject', 'frequency', 'expire_delay', 'reminder')
        );
    }

    /*
    * Save the configuration
    */
    public function save(Request $request)
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // read request
        $mail_from = request('mail_from');
        $mail_subject = request('mail_subject');
        $frequency = request('frequency');
        $expire_delay = request('expire_delay');
        $reminder = request('reminder');

        switch ($request->input('action')) {
            case 'save':
                // put in config file
                config(['deming.notification.mail-from' => $mail_from]);
                config(['deming.notification.mail-subject' => $mail_subject]);
                config(['deming.notification.frequency' => $frequency]);

                config(['deming.notification.expire-delay' => $expire_delay]);
                config(['deming.notification.reminder' => $reminder]);

                // Save configuration
                $text = '<?php return ' . var_export(config('deming'), true) . ';';
                file_put_contents(config_path('deming.php'), $text);

                // Return
                $msg = 'Configuration saved !';
                break;

            case 'test':
                // send test email alert
                $message = '<html><body><br>This is a test message !<br><br></body></html>';

                // define the header
                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html;charset=iso-8859-1',
                    'From: '. $mail_from,
                ];

                // En-têtes additionnels
                if (mail(Auth::User()->email, '=?UTF-8?B?' . base64_encode($mail_subject) . '?=', $message, implode("\r\n", $headers), ' -f'. $mail_from)) {
                    $msg = 'Mail sent to ' . Auth::User()->email;
                } else {
                    $msg = 'Could not send email.';
                }
                break;

            case 'cancel':
                return redirect('/');

            default:
                $msg = 'No actions made.';
        }

        return view(
            'config',
            compact('mail_from', 'mail_subject', 'frequency', 'expire_delay', 'reminder')
        )
            ->withErrors($msg);
    }
}

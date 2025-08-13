<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

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
        $mail_content = config('deming.notification.mail-content');
        $frequency = config('deming.notification.frequency');
        $expire_delay = config('deming.notification.expire-delay');
        $reminder = config('deming.notification.reminder');

        // set emply message
        $message = null;

        // Return
        return view(
            'config',
            compact(
                'mail_from',
                'mail_subject',
                'mail_content',
                'frequency',
                'expire_delay',
                'reminder',
                'message'
            )
        );
    }

    /*
    * Save the configuration
    */
    public function save(Request $request)
    {
        abort_if(Auth::User()->role !== 1, Response::HTTP_FORBIDDEN, '403 Forbidden');

        // Error and message variables
        $errors = Collect();
        $messages = Collect();

        // read request
        $mail_from = request('mail_from');
        $mail_subject = request('mail_subject');
        $mail_content = request('mail_content');
        $frequency = request('frequency');
        $expire_delay = request('expire_delay');
        $reminder = request('reminder');

        switch ($request->input('action')) {
            case 'save':
                // put in config file
                config(['deming.notification.mail-from' => $mail_from]);
                config(['deming.notification.mail-subject' => $mail_subject]);
                config(['deming.notification.mail-content' => $mail_content]);
                config(['deming.notification.frequency' => $frequency]);

                config(['deming.notification.expire-delay' => $expire_delay]);
                config(['deming.notification.reminder' => $reminder]);

                // Save configuration
                $text = '<?php return ' . var_export(config('deming'), true) . ';';
                file_put_contents(config_path('deming.php'), $text);

                // Return
                $messages->push('Configuration saved !');
                break;

            case 'test':
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    // Set the SMTP server
                    $mail->Host = config('mail.mailers.smtp.host');
                    // TCP port to connect to
                    $mail->Port = config('mail.mailers.smtp.port');
                    // Enable SMTP authentication
                    $mail->SMTPAuth = config('mail.smtp.auth');
                    $mail->Username = config('mail.mailers.smtp.username');
                    $mail->Password = config('mail.mailers.smtp.password');
                    // SMTP Security
                    $mail->SMTPSecure = config('mail.mailer.smtp.secure');
                    $mail->SMTPAutoTLS = config('mail.mailer.smtp.auto_tls');

                    // Recipients
                    $mail->setFrom($mail_from);
                    $mail->addAddress(Auth::user()->email);         // Add a recipient

                    // Define charset
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';

                    // Content
                    $mail->isHTML(true);                            // Set email format to HTML
                    $mail->Subject = $mail_subject;
                    $mail->Body = $mail_content;
                    // $mail->AltBody = 'This is the plain text version of the email body';

                    // Optional: Add DKIM signing
                    $mail->DKIM_domain = config('mail.dkim.domain');
                    $mail->DKIM_private = config('mail.dkim.private');
                    $mail->DKIM_selector = config('mail.dkim.selector');
                    $mail->DKIM_passphrase = config('mail.dkim.passphrase');
                    $mail->DKIM_identity = $mail->From;

                    // Send email
                    $mail->send();

                    $messages->push('Message has been sent.');
                } catch (Exception $e) {
                    $errors->push('Message could not be sent.');
                    $errors->push("Mailer Error: {$mail->ErrorInfo}");
                }

                break;

            case 'cancel':
                return redirect('/');

            default:
                $messages->push('No actions made.');
        }

        return view(
            'config',
            compact('mail_from', 'mail_subject', 'mail_content', 'frequency', 'expire_delay', 'reminder')
        )
            ->with('messages', $messages)
            ->with('errors', $errors);
    }
}

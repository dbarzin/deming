<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

        // Return
        return view(
            'config',
            compact(
                'mail_from', 'mail_subject', 'mail_content',
                'frequency', 'expire_delay', 'reminder')
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
                $msg = 'Configuration saved !';
                break;

            case 'test':
                // Create a new PHPMailer instance
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();                               // Use SMTP
                    $mail->Host       = env('MAIL_HOST');          // Set the SMTP server
                    $mail->SMTPAuth   = env('MAIL_AUTH');          // Enable SMTP authentication
                    $mail->Username   = env('MAIL_USERNAME');      // SMTP username
                    $mail->Password   = env('MAIL_PASSWORD');      // SMTP password
                    $mail->SMTPSecure = env('MAIL_SMTPSECURE');    // Enable TLS encryption, `ssl` also accepted
                    $mail->Port       = env('MAIL_PORT');          // TCP port to connect to

                    // Recipients
                    $mail->setFrom($mail_from);
                    $mail->addAddress(Auth::user()->email);         // Add a recipient

                    // Content
                    $mail->isHTML(true);                            // Set email format to HTML
                    $mail->Subject = $mail_subject;
                    $mail->Body    = $mail_content;
                    // $mail->AltBody = 'This is the plain text version of the email body';

                    // Optional: Add DKIM signing
                    $mail->DKIM_domain = env('MAIL_DKIM_DOMAIN');
                    $mail->DKIM_private =  env('MAIL_DKIM_PRIVATE');
                    $mail->DKIM_selector = env('MAIL_DKIM_SELECTOR');
                    $mail->DKIM_passphrase = env('MAIL_DKIM_PASSPHRASE');
                    $mail->DKIM_identity = $mail->From;

                    // Send email
                    $mail->send();

                    $msg = 'Message has been sent';
                } catch (Exception $e) {
                    $msg = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                break;

            case 'cancel':
                return redirect('/');

            default:
                $msg = 'No actions made.';
        }

        return view(
            'config',
            compact('mail_from', 'mail_subject', 'mail_content', 'frequency', 'expire_delay', 'reminder')
        )
            ->withErrors($msg);
    }
}

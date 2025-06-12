<?php

namespace App\Console\Commands;

use App\Models\Control;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deming:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for controls';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('SendNotifications - Start.');

        // Need to send notifications today ?
        if ($this->needCheck()) {
            Log::info('SendNotifications - notifications today');

            $controls = Control
                ::where('status', 0)
                    ->where('plan_date', '<=', Carbon::today()
                        ->addDays(intval(config('deming.notification.expire-delay')))->toDateString())
                    ->orderBy('plan_date')
                    ->count();

            Log::info(
                'SendNotifications - ' .
                $controls .
                ' control(s) will expire within '.
                config('deming.notification.expire-delay') .
                ' days.'
            );

                // Loop on all users
                $users = User::all();

                foreach ($users as $user) {
                    // get controls
                    $controls = Control::where('status', 0)
                        ->leftJoin('control_user', 'controls.id', '=', 'control_user.control_id')
                        ->leftJoin('control_user_group', 'controls.id', '=', 'control_user_group.control_id')
                        ->leftJoin('user_user_group', 'control_user_group.user_group_id', '=', 'user_user_group.user_group_id')
                        ->where(function ($query) use ($user) {
                            $query->where('control_user.user_id', '=', $user->id)
                                ->orWhere('user_user_group.user_id', '=', $user->id);
                        })
                        ->where('plan_date', '<=', Carbon::today()
                            ->addDays(intval(config('deming.notification.expire-delay')))->toDateString())
                        ->orderBy('plan_date')
                        ->get();

                    if ($controls->count() > 0) {
                        App::setlocale($user->language);
                        $txt = '';
                        foreach ($controls as $control) {
                            // Date
                            $txt .= '<a href="' . url('/bob/show/'. $control->id) . '">';
                            $txt .= '<b>';
                            if (strtotime($control->plan_date) >= strtotime('today')) {
                                $txt .= "<font color='green'>" . $control->plan_date .' </font>';
                            } else {
                                $txt .= "<font color='red'>" . $control->plan_date . '</font>';
                            }
                            $txt .= '</b>';
                            $txt .= '</a>';
                            // Space
                            $txt .= ' &nbsp; - &nbsp; ';
                            // Clauses
                            foreach ($control->measures() as $measure) {
                                $txt .= '<a href="' . url('/alice/show/' . $measure->id) . '">'. htmlentities($measure->clause) . '</a>';
                                // Space
                                $txt .= ' &nbsp; ';
                            }
                            $txt .= ' - &nbsp; ';
                            // Name
                            $txt .= htmlentities($control->name);
                            $txt .= "<br>\n";
                        }

                    try {
                        // Create a new PHPMailer instance
                        $mail = new PHPMailer(true);

                        // Server settings
                        $mail->isSMTP();                                     // Use SMTP
                        $mail->Host = env('MAIL_HOST');               // Set the SMTP server
                        $mail->SMTPAuth = env('MAIL_AUTH');               // Enable SMTP authentication
                        $mail->Username = env('MAIL_USERNAME');           // SMTP username
                        $mail->Password = env('MAIL_PASSWORD');           // SMTP password
                        $mail->SMTPSecure = env('MAIL_SMTP_SECURE', false);  // Enable TLS encryption, `ssl` also accepted
                        $mail->SMTPAutoTLS = env('MAIL_SMTP_AUTO_TLS');      // Enable auto TLS
                        $mail->Port = env('MAIL_PORT');               // TCP port to connect to

                        // Recipients
                        $mail->setFrom(config('deming.notification.mail-from'));
                        $mail->addAddress($user->email);
                        $mail->Subject = config('deming.notification.mail-subject');

                        // Get message model
                        $message = config('deming.notification.mail-content');
                        if (($message === null) || (strlen($message) === 0)) {
                            $message = trans('cruds.config.notifications.message_default_content');
                        }

                        // Replace %table% in message model
                        $message = str_replace('%table%', $txt, $message);

                        // Content
                        $mail->isHTML(true);
                        $mail->Body = $message;

                        // Optional: Add DKIM signing
                        $mail->DKIM_domain = env('MAIL_DKIM_DOMAIN');
                        $mail->DKIM_private = env('MAIL_DKIM_PRIVATE');
                        $mail->DKIM_selector = env('MAIL_DKIM_SELECTOR');
                        $mail->DKIM_passphrase = env('MAIL_DKIM_PASSPHRASE');
                        $mail->DKIM_identity = $mail->From;

                        // Send email
                        $mail->send();

                        // Success
                        Log::info("Mail sent to {$user->email}");
                    } catch (Exception $e) {
                        // Log error
                        Log::error("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
                    }
                }
        }
        } else {
            Log::info('SendNotifications - no notifications today');
        }

        Log::info('SendNotifications - DONE.');
    }

    /**
     * return true if check is needed
     *
     * @return bool
     */
    private function needCheck()
    {
        $check_frequency = config('deming.notification.frequency');

        Log::info('SendNotifications - frequency=' . $check_frequency . ' day=' . Carbon::today()->day . ' dayOfWeek=' . Carbon::today()->dayOfWeek);

        return ($check_frequency === '1') ||
            // Weekly
            (($check_frequency === '7') && (Carbon::today()->dayOfWeek === 1)) ||
            // Every two weeks
            (($check_frequency === '15') && ((Carbon::today()->day === 1) || (Carbon::today()->day === 15))) ||
            // Monthly
            (($check_frequency === '30') && (Carbon::today()->day === 1));
    }
}

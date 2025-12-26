<?php

namespace App\Services;

use Error;
use Exception;
use App\Models\Process;
use App\Models\ErrorDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Support\Facades\Auth;

class ProcessServices
{

    public static function createProcess(string $procName, array $options): ?Process
    {
        $default = [
            'intiTime' => date('H:i:s'),
            'notiAdmi' => false,
            'notiUser' => false,
        ];

        $params = array_merge($default, $options);

        $process   = null;
        if (empty($procName)) {
            return null;
        }
        try {
            $process = Process::create([
                'name'         => $procName,
                'init_time'    => $params['intiTime'],
                'created_by'   => Auth::user()->id ?? 1,
                'notify_admin' => $params['notiAdmi'],
                'notify_user'  => $params['notiUser'],
            ]);
            info('Process created: ' . $process->name.' with id: '.$process->id);
        } catch (Error $e) {
            $erroMess = $e->getMessage();
            $indiOkey = false;
            Log::error('Error creating process: '.$erroMess);
        } catch (Exception $e) {
            $erroMess = $e->getMessage();
            $indiOkey = false;
            Log::error('Error creating process: '.$erroMess);
        }
        return $process;
    }

    public static function closeProcess(Process $process, array $options)
    {
        // info('Closing process options: '.json_encode($options));

        $microInit = strtotime($process->init_time);
        $microEnd  = microtime(true);
        $default = [
            'comments'     => 'without comments',
            'qtyProcessed' => 0,
            'qtyErrors'    => 0,
            'endTime'      => date('H:i:s'),
            'timeConsumed' => format_period($microEnd, $microInit),
        ];

        $params = array_merge($default, $options);

        // info('Closing process params: '.json_encode($params));

        $comments = str_replace('<br>', ' | ', $params['comments']);

        $process->update([
            'end_time'          => $params['endTime'],
            'comments'          => $comments,
            'processed_records' => $params['qtyProcessed'],
            'qty_errors'        => $params['qtyErrors'],
            'time_consumed'     => $params['timeConsumed'],
        ]);

        if (self::countFail($process->id) > 0) {
            self::errorReporting($process);
            // self::sendSlackNotification($process);
        }
    }

    public static function errorReporting(Process $process)
    {

        $to = config('sport.emails.pending-l2');

        $subject = 'Error Report: ' . $process->name;

        $html = '<h1>Error Report</h1>';
        $html .= '<p><strong>Process:</strong> ' . $process->name . '</p>';
        $html .= '<p><strong>Comments:</strong> ' . $process->comments . '</p>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>Reference</th>';
        $html .= '<th>Message</th>';
        $html .= '<th>Comment</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $errors = ErrorDetail::byProcess($process->id)->get();
        foreach ($errors as $error) {
            $html .= '<tr>';
            $html .= '<td>' . $error->reference . '</td>';
            $html .= '<td>' . $error->message . '</td>';
            $html .= '<td>' . $error->comment . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody>';
        $html .= '</table>';

        Mail::html($html, function ($message) use ($to, $subject) {
            $message->to($to)
                ->subject($subject);
        });
    }

    // private static function sendSlackNotification(Process $process)
    // {
    //     $errors = ErrorDetail::byProcess($process->id)->get();
    //     $message = "Error Report: {$process->name}\n";
    //     $message .= "Comments: {$process->comments}\n";

    //     Notification::route('slack', config('services.slack.webhook_url'))
    //         ->notify(new class($message, $errors) extends \Illuminate\Notifications\Notification {
    //             private $message;
    //             private $errors;

    //             public function __construct($message, $errors)
    //             {
    //                 $this->message = $message;
    //                 $this->errors = $errors;
    //             }

    //             public function via($notifiable)
    //             {
    //                 return ['slack'];
    //             }

    //             public function toSlack($notifiable)
    //             {
    //                 return (new SlackMessage)
    //                     ->from('Error Bot', ':ghost:')
    //                     ->to('#errors')
    //                     ->content($this->message)
    //                     ->attachment(function ($attachment) {
    //                         foreach ($this->errors as $error) {
    //                             $attachment->fields([
    //                                 'Reference' => $error->reference,
    //                                 'Message' => $error->message,
    //                                 'Comment' => $error->comment,
    //                             ]);
    //                         }
    //                     });
    //             }
    //         });
    // }

    public static function countFail($processId)
    {
        return ErrorDetail::countByProcess($processId);
    }


}

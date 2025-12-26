<?php

namespace App\Classes;

use App\Models\Process;
use App\Models\ErrorDetail;
use App\Models\Traits\JsonData;
use Illuminate\Support\Facades\Log;
use App\Services\ProcessServices as PS;

/**
 * This class is used to create a response object for a process.
 * If class receives the name of the process a record in the database
 * will be created for tracking the process.
 */
final class PResponse
{

    use JsonData;

    protected $process;

    public function __construct(
        public string $processName = '',
        public $notiAdmin = false,
        public $notifyAdmin = false,
        public bool $indiOkey = true,
        public bool $okay = true,
        public string $userMess = '',
        public string $userMessage = '',
        public int $qtyProcessed = 0,
        public int $qtyErrors = 0,
        public array $errors = [],
        public $data = '{}',
        public int $processId = 0,
    ) {
        $this->data = is_string($data) ? $data : json_encode($data);

        //---------------------------------------------------------
        // If the process name is not empty, create a new process
        //---------------------------------------------------------
        if ($this->processName !== '') {
            $options = ['notiAdmi' => $this->notifyAdmin];
            $this->process = PS::createProcess($this->processName, $options);
            if ($this->process === null) {
                Log::error('Failed to create the process: ' . $this->processName);
                exit();
            }
            $this->processId = $this->process->id;
        }
    }

    protected $casts = [
        'id' => 'integer',
        'okay' => 'boolean',
        'data' => 'array',
    ];

    public function __toString(): string
    {
        return json_encode([
            'processName' => $this->processName,
            'notiAdmin' => $this->notiAdmin,
            'notifyAdmin' => $this->notifyAdmin,
            'indiOkey' => $this->indiOkey,
            'okay' => $this->okay,
            'userMess' => $this->userMess,
            'qtyProcessed' => $this->qtyProcessed,
            'qtyErrors' => $this->qtyErrors,
            'data' => $this->data,
        ]);
    }

    public function close()
    {
        if ($this->process instanceof Process) {
            //------------------------------------------------
            // Closing the process and updating the comments
            //------------------------------------------------
            $options = [
                'comments' => str_replace('<br>', ' | ', $this->userMess),
                'qtyProcessed' => $this->qtyProcessed,
                'qtyErrors' => $this->qtyErrors,
                'errors' => $this->errors,
            ];

            if (count($this->errors) > 0) {
                info('There were ' . count($this->errors) . ' errors during the process');
                info(print_r($this->errors, true));

                $dbErrors = [];
                foreach ($this->errors as $error) {
                    $dbErrors[] = [
                        'process_id' => $this->process->id,
                        'reference' => $error['reference'] ? length($error['reference'], 100) : 'No Reference',
                        'message' => $error['message'] ? length($error['message'], 250) : 'No Error Message',
                        'comment' => $error['comment'] ? length($error['comment'], 500) : 'No Comment',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                ErrorDetail::insert($dbErrors);
            }

            PS::closeProcess($this->process, $options);
        }
    }
}

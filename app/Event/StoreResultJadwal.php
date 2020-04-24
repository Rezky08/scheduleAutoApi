<?php

namespace App\Event;

use App\Http\Controllers\API\AlgenResultLogController;
use App\Http\Controllers\API\ProcessLogController;
use App\Http\Controllers\API\ProcessLogDetailController;
use App\ProcessLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StoreResultJadwal
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $JadwalResult;
    public $process;
    public $process_log_controller;
    public $process_log_detail_controller;
    public $algen_result_controller;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProcessLog $process, $JadwalResult)
    {
        $this->process = $process;
        $this->JadwalResult = $JadwalResult;
        $this->process_log_controller = new ProcessLogController();
        $this->process_log_detail_controller = new ProcessLogDetailController();
        $this->algen_result_controller = new AlgenResultLogController();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

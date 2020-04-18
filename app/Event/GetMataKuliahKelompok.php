<?php

namespace App\Event;

use App\Http\Controllers\API\ProcessLogController;
use App\Http\Controllers\API\ProcessLogDetailController;
use App\Peminat;
use App\ProcessLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GetMataKuliahKelompok
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $process;
    public $peminat;
    public $config;
    public $headers;
    public $process_log_controller;
    public $process_log_detail_controller;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProcessLog $process, Peminat $peminat, $config)
    {
        foreach ($config as $key => $value) {
            $config[$key] = (int) $value;
        }
        $this->process = $process;
        $this->peminat = $peminat;
        $this->config = $config;
        $this->headers = [
            'headers' => [
                'Host' => 'server.python'
            ]
        ];
        $this->process_log_controller = new ProcessLogController();
        $this->process_log_detail_controller = new ProcessLogDetailController();
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

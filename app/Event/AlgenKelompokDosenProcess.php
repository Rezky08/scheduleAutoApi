<?php

namespace App\Event;

use App\Peminat;
use App\ProcessLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AlgenKelompokDosenProcess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $peminat;
    public $process;
    public $config;
    public $kelompok_matkul;
    public $headers;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ProcessLog $process, Peminat $peminat, $config, $kelompok_matkul)
    {
        $config['rules']['max_kelompok'] = (int) $config['rules']['max_kelompok'];
        $config['num_generation'] = (int) $config['num_generation'];
        $config['num_population'] = (int) $config['num_population'];
        $config['crossover_rate'] = (float) $config['crossover_rate'];
        $config['mutation_rate'] = (float) $config['mutation_rate'];
        $config['timeout'] = (int) $config['timeout'];

        $this->process = $process;
        $this->peminat = $peminat;
        $this->config = $config;
        $this->kelompok_matkul = $kelompok_matkul;
        $this->headers = [
            'headers'=>[
                'Host'=>'server.python'
            ]
        ];
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

<?php

namespace App\Helpers;

class Host
{
    public function host($host_name)
    {
        $hosts = [
            'python_engine' => 'http://192.168.1.11/'
        ];
        try {
            return $hosts[$host_name];
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

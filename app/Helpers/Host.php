<?php

namespace App\Helpers;

class Host
{
    public function host($host_name)
    {
        $hosts = [
            'python_engine' => 'http://127.0.0.1:5000/'
        ];
        try {
            return $hosts[$host_name];
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}

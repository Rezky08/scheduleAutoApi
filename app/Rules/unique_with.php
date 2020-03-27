<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class unique_with implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = explode(',', $params);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $table_name = array_shift($this->params);
        $whereCond = [];
        while ($field = array_shift($this->params)) {
            $whereCond[$field] = array_shift($this->params);
            if (strtolower($whereCond[$field]) === 'null') {
                $whereCond[$field] = null;
            }
        }
        $getData = DB::table($table_name)->where($whereCond)->first();
        if (is_null($getData)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute must be unique';
    }
}

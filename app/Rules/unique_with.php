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
    protected $message = ':attribute has already been taken.';
    public function __construct($params, $ignore = null, $message = '')
    {
        if ($message != '') {
            $this->message = $message;
        }
        $this->params = explode(',', $params);
        $this->ignore = explode(',', $ignore);
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
        while ($field = array_shift($this->ignore)) {
            $whereCond[] = [$field, '!=', array_shift($this->ignore)];
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
        return $this->message;
    }
}

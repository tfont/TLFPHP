<?php

class Functional_Returner
{
    private $parameters;
    private $arguments;
    private $function_name;

    public function __construct(array $parameters, array $arguments, $function_name)
    {
        $this->parameters    = $parameters;
        $this->arguments     = $arguments;
        $this->function_name = $function_name;
    }

    public function returning($return_datatype)
    {
        /*
        var_dump($this->parameters);
        var_dump($this->arguments);
        var_dump($this->function_name);
        var_dump($return_datatype);
        */

        #var_dump('@@@@');
        #var_dump($this->parameters);
        #var_dump($this->arguments);

        #var_dump('----------');
        $key = 0;
        foreach ($this->parameters as $parameter => $datatype)
        {
            // mutliple values have been set as argument (via array)
            if (is_array($datatype))
            {
                // more than one data type
                if (count($datatype) > 1)
                {
                    $valid_type = FALSE;

                    // multi data type check here
                    foreach ($datatype as $type)
                    {
                        // create a case for each data type! eventually this will become a static class to keep codebase DRY
                        switch ($type)
                        {
                            case 'uint8':
                                if (is_int($this->arguments[$key]) === TRUE && ((int) $this->arguments[$key] >= 0) && ((int) $this->arguments[$key] <= 255))
                                {
                                    $valid_type = TRUE;
                                }
                                break;
                            case 'bool':
                                if (is_bool($this->arguments[$key]) === TRUE)
                                {
                                    $valid_type = TRUE;
                                }
                                break;
                        }
                    }

                    if ($valid_type === FALSE)
                    {
                        trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' contains an invalid data type count. Function execution failed.', E_USER_ERROR);
                    }
                }
                else
                {
                    trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' contains an invalid data type count. Must contain 1 or an array value of ONLY 2. Function execution failed.', E_USER_ERROR);
                }
            }
            else // single value has been set an argument
            {
                #var_dump($datatype);
                // create a case for each data type! eventually this will become a static class to keep codebase DRY
                switch ($datatype)
                {
                    case 'uint8':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < 0 || ((int) $this->arguments[$key]) > 255)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not uint8. Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'uint16':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < 0 || ((int) $this->arguments[$key]) > 65535)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not uint16. Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'uint32':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < 0 || ((int) $this->arguments[$key]) > 16777215)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not uint32. Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'uint64':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < 0 || ((int) $this->arguments[$key]) > 18446744073709551615)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not uint64 Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'int8':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < -128 || ((int) $this->arguments[$key]) > 127)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not int8. Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'int16':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < -32768 || ((int) $this->arguments[$key]) > 32767)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not int16. Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'int32':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < -8388608 || ((int) $this->arguments[$key]) > 8388607)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not int32. Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'int64':
                        if (is_int($this->arguments[$key]) === FALSE || ((int) $this->arguments[$key]) < -9223372036854775808  || ((int) $this->arguments[$key]) > 9223372036854775807)
                        {
                            trigger_error('Function: '.$this->function_name.'() at parameter \''.$parameter.'\' is not int64 Function execution failed.', E_USER_ERROR);
                        }
                        break;
                    case 'string':
                        if (is_string($this->arguments[$key]) === FALSE || strlen($this->arguments[$key]) > 255) // is_string()
                        {
                            trigger_error('Function: '.$this->function_name.'() return value is not string. Function execution failed.', E_USER_ERROR);
                        }
                }
            }

            $key++;
        }

        $return_data = call_user_func_array($this->function_name, $this->arguments);

        // mutliple values have been set as return data type (via array)
        if (is_array($return_datatype))
        {
            // more than one data type
            if (count($return_datatype) > 1)
            {
                $valid_type = FALSE;

                // multi data type check here
                foreach ($return_datatype as $type)
                {
                    // create a case for each data type! eventually this will become a static class to keep codebase DRY
                    switch ($type)
                    {
                        case 'uint8':
                            if (is_int($return_data) === TRUE && ((int) $return_data >= 0) && ((int) $return_data <= 255))
                            {
                                $valid_type = TRUE;
                            }
                            break;
                        case 'bool':
                            if (is_bool($return_data) === TRUE)
                            {
                                $valid_type = TRUE;
                            }
                            break;
                    }
                }

                if ($valid_type === FALSE)
                {
                    trigger_error('Function: '.$this->function_name.'() return value contains an invalid data type count. Function execution failed.', E_USER_ERROR);
                }
            }
            else
            {
                trigger_error('Function: '.$this->function_name.'() return value contains an invalid data type count. Must contain 1 or an array value of ONLY 2. Function execution failed.', E_USER_ERROR);
            }
        }

        // create a case for each data type! eventually this will become a static class to keep codebase DRY
        switch ($return_datatype)
        {
            case 'string':
                if (is_string($return_data) === FALSE || strlen($return_data) > 255)
                {
                    trigger_error('Function: '.$this->function_name.'() return value is not string. Function execution failed.', E_USER_ERROR);
                }
                break;
            case 'text':
                if (is_string($return_data) === FALSE || strlen($return_data) > 65535)
                {
                    trigger_error('Function: '.$this->function_name.'() return value is not text. Function execution failed.', E_USER_ERROR);
                }
                break;
        }

        return $return_data;
    }
}

class Functional_Parameters
{
    private $parameters;
    private $function_name;

    public function __construct(array $parameters, $function_name)
    {
        $this->parameters    = $parameters;
        $this->function_name = $function_name;
    }

    public function with()
    {
        // retrieves the argement values
        $arguments = func_get_args();

        return new Functional_Returner($this->parameters, $arguments, $this->function_name);
    }

    // mirrors returning() of Functional_Returner (REQUIRES TESTING)
    public function returning($return_datatype)
    {
               $functional_returner = new Functional_Returner($this->parameters, array(), $this->function_name);
        return $functional_returner->returning($return_datatype);
    }
}

class Functional_Caller
{
    private $parameters;

    public function __construct(array $parameters)
    {
        // TODO: remove $ if the first character of the key contains $
        $this->parameters = $parameters;
    }

    public function call($function_name)
    {
        return new Functional_Parameters($this->parameters, $function_name);
    }
}

<?Php

//  converts multi-dim objects into multi-arrays

function convert_objects_to_arrays($obj, &$arr = array())
{
    if (!is_object($obj) && !is_array($obj))
    {
        $arr = $obj;

        return $arr;
    }

    foreach ($obj as $key => $value)
    {
        if (!empty($value))
        {
            $arr[$key] = array();

            convert_objects_to_arrays($value, $arr[$key]);
        }
        else
        {
            $arr[$key] = $value;
        }
    }

    return (array) $arr;
}
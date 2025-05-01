<?php

namespace core;

class OutletsLogic
{
    public static function Error_Order_Search($outlet_address) : array
    {
        $errors = [];

        if($outlet_address == "")
        {
            $errors['form'] = "Не все формы заполнены";
        }

        return $errors;
    }
}
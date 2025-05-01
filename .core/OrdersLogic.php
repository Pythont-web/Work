<?php

namespace core;
class OrdersLogic
{
    public static function Error_Order_Search($id, $name, $outlet_id, $order_composition, $price, $file) : array
    {
        $errors = [];
        $Massive = [$name, $outlet_id, $order_composition, $price, $file];
        $type = ['image/png', 'image/jpeg'];

        for($i = 0; $i < count($Massive) - 1; $i++)
        {
            if($Massive[$i] == "")
            {
                $errors['form'] = "Не все формы заполнены";
            }
        }

        if($id != "" && $file['size'] == 0)
        {
            return $errors;
        }

        if($file['size'] == 0)
        {
            $errors['file'] = "Файл не выбран";
        }

        else if(!in_array($file['type'], $type))
        {
            $errors['file_type'] = "Тип файла должен быть jpg или png";
        }

        return $errors;
    }
}
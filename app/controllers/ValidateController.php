<?php
namespace Application\Controllers;

use Application\Core\Controller;

/*
 * Проверка полей формы
 */
class ValidateController extends Controller
{

    // в функцию передается массив $_GET и возвращается JSON результат проверки
    public function checkJoin(array $get)
    {
        $data = $get["data"];
        $type = $get["type"];
        echo $this->auth->check_input($data, $type);
    }

}

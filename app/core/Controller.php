<?php
namespace Application\Core;

use Application\Models\AuthModel;

class Controller
{
    
    protected $error = null;
    protected $data = null;
    protected $auth;
    protected $view;

    /**
     * 
     * @param type $config_path
     * @param type $section_name
     */
    function __construct()
    {
        $this->auth = new AuthModel();
        $this->view = new View;
    }

    // Проверка авторизации
    protected function checkAuth()
    {
        if (!$this->auth->authorization()) {
            //~ совершаем процедуру выхода
            $this->auth->exit_user();
        }
    }

}

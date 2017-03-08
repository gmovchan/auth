<?php

class AuthController
{

    private $error = null;
    private $data = null;
    private $view;
    private $auth;

    function __construct()
    {
        $this->auth = new AuthModel(__DIR__ . '/../configs/app.ini', 'vagrant');
        $this->view = new View;
    }

    /**
     * метод открывает форму аутентификации, если пользователь не прошёл авторизацию
     */
    public function getPage()
    {
        // Авторизация
        if (isset($_POST['send'])) {
            if (!$this->auth->authentication()) {
                $this->error = $this->auth->getErrors();
            }
        }

        // Выход
        if (isset($_GET['exit'])) {
            $this->auth->exit_user();
        }

        if ($this->auth->authorization()) {
            $this->data['login'] = $_SESSION['login_user'];
            $this->view->generate('/auth/successfulAuth.php', 'authTemplate.php', $this->data, $this->error);
        } else {
            $this->view->generate('/auth/AuthForm.php', 'authTemplate.php', $this->data, $this->error);
        }
    }

    public function logout()
    {
        $this->auth->exit_user();
    }

}

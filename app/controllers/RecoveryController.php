<?php
namespace Application\Controllers;

use Application\Core\Controller;

/**
 * Восстановление пароля
 */
class RecoveryController extends Controller
{

    public function getPage()
    {
        if (isset($_POST['send'])) {
            $reply = $this->auth->recovery_pass($_POST['login'], $_POST['mail']);
            if ($reply == 'good') {
                $this->view->generate('/auth/recoverySuccessful.php', 'authTemplate.php');
            } else {
                $this->error = $this->auth->getErrors();
                $this->view->generate('/auth/recoveryForm.php', 'authTemplate.php', $this->data, $this->error);
            }
        } else {
            $this->view->generate('/auth/recoveryForm.php', 'authTemplate.php');
        }
    }

}

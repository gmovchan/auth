<?php
namespace Application\Controllers;

use Application\Core\Controller;

class IndexController extends Controller
{

    public function getPage()
    {
        $this->checkAuth();
        $this->view->generate('/index.php', 'authTemplate.php');
    }

}

<?php

class IndexController extends Controller
{

    public function getPage()
    {
        $this->checkAuth();
        $this->view->generate('/index.php', 'authTemplate.php');
    }
    
    

}

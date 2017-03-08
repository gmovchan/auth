<?php

class Route
{
    
    public function start()
    {
        $controllerName = 'Index';
        $actionName = 'getPage';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[1])) {
            // делает первую блукву прописной,остальные строчными, потому что 
            // так выглядят названия соответствующих классов и файлов, например AuthModel
            $controllerName = ucfirst(strtolower($routes[1]));
        }

        if (!empty($routes[2])) {
            $actionName = strtolower($routes[2]);
        }

        $modelName = $controllerName . 'Model';
        $controllerName = $controllerName . 'Controller';
        //var_dump($controllerName);

        $modelFile = strlen($modelName) . '.php';
        $modelPath = __DIR__ . '/../models/' . $modelFile;

        if (file_exists($modelPath)) {
            ////var_dump($modelPath);
            require $modelPath;
        }

        $controllerlFile = $controllerName . '.php';
        //var_dump($controllerlFile);
        $controllerPath = __DIR__ . '/../controllers/' . $controllerlFile;
        
        if (file_exists($controllerPath)) {
            //var_dump($controllerPath);
            require $controllerPath;
        } else {
            //var_dump('getErrorPage404');
            //var_dump($controllerPath);
            $this->getErrorPage404();
        }

        $controller = new $controllerName;
        $action = $actionName;
        //var_dump($action);

        if (method_exists($controller, $action)) {
            // Проверка авторизации
            /*
            if (!$auth->authorization() and $controller != 'AuthController') {
                //~ совершаем процедуру выхода
                $auth->exit_user();
            }
            */
            $controller->$action();
        } else {
            $this->getErrorPage404();
        }
    }
    
    private function getErrorPage404()
        {
            ////var_dump('ErrorPage');
            $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            header('Location:' . $host . '404');
            exit;
        }

}

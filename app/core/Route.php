<?php

namespace Application\Core;

class Route
{

    public function start()
    {
        $get = null;
        $controllerName = 'Index';
        $actionName = 'getPage';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[1])) {
            // делает первую блукву прописной, остальные строчными, потому что 
            // так выглядят названия соответствующих классов и файлов, например AuthModel
            $controllerName = ucfirst(strtolower($routes[1]));
        }

        if (!empty($routes[2])) {
            /*
             * если передаются get переменные, то они будут отделены от названия метода
             * и переданы в качестве аргумента в его вызов
             */
            if (isset($_GET)) {
                $get = $_GET;
                // отделяет имя метода от переменных
                $routes[2] = explode('?', $routes[2]);
                $routes[2] = $routes[2][0];
            }
            $actionName = strtolower($routes[2]);
        }
        
        $controllerlClass = $controllerName . 'Controller';
        $controllerNamespace = 'Application\\Controllers\\' . $controllerlClass;
        if (!class_exists($controllerNamespace)) {
            $this->getErrorPage404();
        }

        $controller = new $controllerNamespace;
        $action = $actionName;

        if (method_exists($controller, $action)) {
            $controller->$action($get);
        } else {
            $this->getErrorPage404();
        }
    }

    private function getErrorPage404()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        header('Location:' . $host . '404');
        exit;
    }

    /*
     * Старый роутер без использования автозагрузчика. На удаление.
     */
    /*
      public function start()
      {
      $get = null;
      $controllerName = 'Index';
      $actionName = 'getPage';

      $routes = explode('/', $_SERVER['REQUEST_URI']);

      if (!empty($routes[1])) {
      // делает первую блукву прописной, остальные строчными, потому что
      // так выглядят названия соответствующих классов и файлов, например AuthModel
      $controllerName = ucfirst(strtolower($routes[1]));
      }

      if (!empty($routes[2])) {
      /*
     * если передаются get переменные, то они будут отделены от названия метода
     * и переданы в качестве аргумента в его вызов
     */
    /*
      if(isset($_GET)) {
      $get = $_GET;
      // отделяет имя метода от переменных
      $routes[2] = explode('?',  $routes[2]);
      $routes[2] = $routes[2][0];
      }
      $actionName = strtolower($routes[2]);
      }

      $modelName = $controllerName . 'Model';
      $controllerName = $controllerName . 'Controller';

      $modelFile = strlen($modelName) . '.php';
      $modelPath = __DIR__ . '/../models/' . $modelFile;

      if (file_exists($modelPath)) {
      require $modelPath;
      }

      $controllerlFile = $controllerName . '.php';
      $controllerPath = __DIR__ . '/../controllers/' . $controllerlFile;

      if (file_exists($controllerPath)) {
      require $controllerPath;
      } else {

      $this->getErrorPage404();
      }

      $controller = new $controllerName;
      $action = $actionName;

      if (method_exists($controller, $action)) {

      $controller->$action($get);
      } else {
      $this->getErrorPage404();
      }
      }
     */
}

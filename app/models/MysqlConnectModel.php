<?php

namespace Application\Models;

use Application\Core\Model;
use PDO;

/**
 * объект класса подключается к БД и работает с запросами
 */
class MysqlConnectModel extends Model{
   
    //хранит подключение к БД для доступа к нему из методов класса
    private $dbh;
    private $config_data;
    
    function __construct($config_path, $section_name)
    {   
        $this->loadConfig($config_path, $section_name);
        $this->connect();
    }

    /**
     * 
     * @param type $config_path
     * @param type $section_name
     * @return type
     */
    private function loadConfig($config_path, $section_name)
    {       
        $config_array = parse_ini_file($config_path, true);
        $config_array =  $config_array[$section_name];
        // присваивает защищенному свойству объекта данные из файла конфигурации
        $this->config_data = $config_array;
    }

    private function connect()
    {
        // отлов ошибок подключения к БД
        // FIXME: плохое использование исключений, перепишу, как узнаю о них больше
        try {
        $this->dbh = new PDO('mysql:host=' . $this->config_data['host'] . ';dbname=' .
                $this->config_data['db'], $this->config_data['user'], $this->config_data['password']);
        // требуется чтобы PDO сообщало об ошибке и прерывало выполнение скрипта
        $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
        echo '<p>' . $e->getMessage() . '</p>';
        exit();
        }
    }

    /**
     * запрос к БД
     * без понятия зачем тут нужен новый уровень абстракции
     * $section_name принимает массив с параметрами для подготавливаемого 
     * запроса с неименованными псевдопеременными для защиты от инъекций
     */
    public function query($query, $type = null, $num = null, array $query_param = array())
    {
        // FIXME: плохое использование исключений, перепишу, как узнаю о них больше
        try {
            if ($q = $this->dbh->prepare($query)) {
                switch ($type) {
                    case 'num_row':
                        $q->execute($query_param);
                        return $q->rowCount();
                        break;

                    case 'result':
                        $q->execute($query_param);
                        return $q->fetchColumn($num);
                        break;

                    case 'accos':
                        $q->execute($query_param);
                        return $q->fetch(PDO::FETCH_ASSOC);
                        break;

                    case 'none':
                        $q->execute($query_param);
                        return $q;
                        break;

                    default:
                        $q->execute($query_param);
                        return $q;
                }
            }
        } catch (PDOException $e) {
            //TODO: убрать при переносе на сервер, строка только для отладки
            echo '<p>' . $query . '</p>';
            echo '<p>' . $e->getMessage() . '</p>';
            exit();
        }
    }

    /**
     * получает путь к файлу конфигурации и возвращает массив
     * если передан $section_name, то возвращает только массив с данными из
     * определенной секции конфига
     */
    /*
    protected function config_load($config_path, $section_name = false)
    {
        if (file_exists($config_path)) {
            $config_array = parse_ini_file($config_path, true);
            if ($section_name) {
                return $config_array[$section_name];
            }
            return $config_array;
        }
    }
    */
}


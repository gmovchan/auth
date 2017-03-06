<?php

/**
 * принимает ссылку на файл конфигурации для подключения к БД
 */
class Mysql
{

    //хранит подключение к БД для доступа к нему из методов класса
    private $dbh;

    function connect($config_path, $section_name)
    {
        //получение данных из файла конфигурации
        $config_data = $this->config_load($config_path, $section_name);
        //отлов ошибок подключения к БД
        try {
            $this->dbh = new PDO('mysql:host=' . $config_data['host'] . ';dbname=' .
                    $config_data['db'], $config_data['user'], $config_data['password']);
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
    function query($query, $type = null, $num = null, array $query_param = array())
    {
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
     * экранирует данные
     * FIXME: лучше переделать на подготавливаемые запросы, функция возвращает
     * строку в кавычках, которая ломает некоторые функции
     */
    function screening($data)
    {
        $data = trim($data);
        return $this->dbh->quote($data);
    }

    /**
     * получает путь к файлу конфигурации и возвращает массив
     * если передан $section_name, то возвращает только массив с данными из
     * определенной секции конфига
     */
    function config_load($config_path, $section_name = false)
    {
        if (file_exists($config_path)) {
            $config_array = parse_ini_file($config_path, true);
            if ($section_name) {
                return $config_array[$section_name];
            }
            return $config_array;
        }
    }

}

?>

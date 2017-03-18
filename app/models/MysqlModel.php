<?php

namespace Application\Models;

use Application\Core\Model;
use PDO;

/**
 * Объект класса подключается к БД и работает с запросами
 * Новый уровень абстракции для работы с БД и запросами необходим, чтобы
 * если изменится способ подключения и настройки, то достаточно было бы изменить
 * только этот класс
 */
class MysqlModel extends Model
{

    //хранит подключение к БД для доступа к нему из методов класса
    private $dbh;
    private $config_data;

    /**
     * 
     * @param Model $classModel класса из которого создается объект MysqlModel
     */
    public function __construct(Model $classModel)
    {
        $this->loadConfig($classModel);
        $this->connect();
    }

    /**
     * возвращает настройки для подключения к БД
     * @param Model $classModel класс из которого создается объекта класса MysqlModel 
     * @return type массив с данными из заданной секции настроек
     */
    private function loadConfig(Model $classModel)
    {
        $config_path = null;
        $section_name = null;

        if ($classModel instanceof AuthModel) {
            $config_path = __DIR__ . '/../configs/app.ini';
            $section_name = 'vagrant';
        }

        /*
         * возможно не требуется выбрасывать тут исключение, потому что 
         * приложение и так ломается на этапе подключения к БД, если не удалось 
         * получить настройки 
         */
        if (is_null($section_name) or is_null($config_path)) {
            throw new \Exception("Для класса ".get_class($classModel)." нет настроек Mysql");
        }
         
        $config_array = parse_ini_file($config_path, true);
        $config_array = $config_array[$section_name];
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
    public function query($query, $typeQuery = null, $num = null, array $query_param = array())
    {
        if ($q = $this->dbh->prepare($query)) {
            switch ($typeQuery) {
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
                    // выкидывает исключение и завершает скрипт, если не найден переданный тип SQL запроса
                    throw new \Exception("Ошибка при указании типа SQL запроса");
                    
            }
        }
    }

}

<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Evento extends Model
{
    const ERROR = 'EventoError';
    const SUCCESS = 'EventoSuccess';

    public static function listAll($limit = "LIMIT 9")
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM events WHERE status = '1' ORDER BY in_order DESC {$limit}");
    }

    public function getWithSlug($slug)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM events WHERE slug = :slug", ['slug' => $slug]);
        $this->setData($results[0]);
    }

    public function save()
    {
        $sql = new Sql();

        $action = empty($this->getid()) ? "insert" : "update";

        $result = $sql->{$action}("events", $this->getValues());

        if ($result && !$this->getid()) $this->setid($result);

        $this->setData($this->getValues());
    }

    public static function setSuccess($msg)
    {
        $_SESSION[Evento::SUCCESS] = $msg;
    }
    public static function getSuccess()
    {
        $msg = (isset($_SESSION[Evento::SUCCESS]) && $_SESSION[Evento::SUCCESS]) ? $_SESSION[Evento::SUCCESS] : '';
        Evento::clearSuccess();
        return $msg;
    }

    public static function setError($msg)
    {
        $_SESSION[Evento::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Evento::ERROR]) && $_SESSION[Evento::ERROR]) ? $_SESSION[Evento::ERROR] : '';
        Evento::clearError();
        return $msg;
    }
    public static function clearSuccess()
    {
        $_SESSION[Evento::SUCCESS] = NULL;
    }
    public static function clearError()
    {
        $_SESSION[Evento::ERROR] = NULL;
    }

    public function get($id): void
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM events WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM events WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM events 
            ORDER BY in_order DESC
            LIMIT $start, $itensPerPage ;
        "
        );

        $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int) $resultTotal[0]['nrtotal'],
            'pages' => ceil($resultTotal[0]['nrtotal'] / $itensPerPage),
        ];
    }
    public static function getPageSearch($search, $page = 1, $itensPerPage = 3)
    {

        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM events 
            WHERE title LIKE :search 
            ORDER BY in_order DESC
            LIMIT $start, $itensPerPage;",
            [
                ':search' => '%' . $search . '%'
            ]
        );

        $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int) $resultTotal[0]['nrtotal'],
            'pages' => ceil($resultTotal[0]['nrtotal'] / $itensPerPage),
        ];
    }
}//End Classe

<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Vestibular extends Model
{
    const ERROR = 'VestibularError';

    /**
     * listAll retorna uma consulta geral com LIMIT.
     * @param string $limit LIMIT 0 de retorno de linhas.
     * @return void
     */
    public static function listAll(array $conditions = [], $fields = "*", $operator = ' AND ', $limit = "")
    {
        $rs = new Sql();

        try {

            $binds = array_keys($conditions);

            $where = null;

            foreach ($binds as $v) {
                if (is_null($where)) {
                    $where .= "AND {$v} = {$conditions[$v]}";
                } else {
                    $where .= "{$operator}{$v} = {$conditions[$v]}";
                }
            }

            $SQL = "SELECT {$fields} FROM vestibulares WHERE TRUE ";

            $SQL .= $where;

            $SQL .=  " ORDER BY id DESC {$limit}";

            return $rs->select($SQL);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function getWithSlug($slug)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM vestibulares WHERE slug = :slug", ['slug' => $slug]);
        $this->setData($results[0]);
    }

    public static function listAllOneLess()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM vestibulares ORDER BY id DESC LIMIT 1,3");
    }
    public static function firstVestibular()
    {
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM vestibulares ORDER BY id DESC LIMIT 1");
        return  $result[0];
    }

    /**
     * Insere o artigo na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();

        $action = empty($this->getid()) ? "insert" : "update";

        $result = $sql->{$action}("vestibulares", $this->getValues());

        if ($result && !$this->getid()) $this->setid($result);

        return $this->setData($this->getValues());
    }


    public static function setError($msg)
    {
        $_SESSION[Vestibular::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Vestibular::ERROR]) && $_SESSION[Vestibular::ERROR]) ? $_SESSION[Vestibular::ERROR] : '';
        Vestibular::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Vestibular::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM vestibulares WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM vestibulares WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM vestibulares 
            ORDER BY id DESC
            LIMIT $start, $itensPerPage;
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
            FROM vestibulares 
            WHERE title LIKE :search 
            ORDER BY title
            LIMIT $start, $itensPerPage;
        ",
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
}//Fim Classe

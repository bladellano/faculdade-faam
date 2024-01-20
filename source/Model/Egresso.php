<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Egresso extends Model
{
    const ERROR = 'EgressoError';
    const TABLE = 'egressos';

    /**
     * Função listAll.
     *
     * @param array $conditions
     * @param string $fields
     * @param string $operator
     * @param string $limit
     * @throws \Exception descrição da exceção
     * @return Some_Return_Value
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

            $SQL = "SELECT {$fields} FROM " . self::TABLE . " WHERE TRUE ";

            $SQL .= $where;

            $SQL .=  " ORDER BY id DESC {$limit}";

            return $rs->select($SQL);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Recupera dados com o slug fornecido do banco de dados e define-os como os dados do objeto.
     *
     * @param tipo_dado $slug descrição do parâmetro slug
     */
    public function getWithSlug($slug)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM " . self::TABLE . " WHERE slug = :slug", ['slug' => $slug]);
        $this->setData($results[0]);
    }
    
    /**
     * Recupera uma lista de registros do banco de dados, excluindo o primeiro registro, com um limite de 3.
     *
     * @return array Registros recuperados do banco de dados
     */
    public static function listAllOneLess()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM " . self::TABLE . " ORDER BY id DESC LIMIT 1,3");
    }

    public static function firstEgresso()
    {
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM " . self::TABLE . " ORDER BY id DESC LIMIT 1");
        return  $result[0];
    }

    public function save()
    {
        $sql = new Sql();

        $action = empty($this->getid()) ? "insert" : "update";

        $result = $sql->{$action}(self::TABLE, $this->getValues());

        if ($result && !$this->getid()) $this->setid($result);

        return $this->setData($this->getValues());
    }

    public static function setError($msg)
    {
        $_SESSION[Egresso::ERROR] = $msg;
    }

    public static function getError()
    {
        $msg = (isset($_SESSION[Egresso::ERROR]) && $_SESSION[Egresso::ERROR]) ? $_SESSION[Egresso::ERROR] : '';
        Egresso::clearError();
        return $msg;
    }

    public static function clearError()
    {
        $_SESSION[Egresso::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM " . self::TABLE . " WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM ' . self::TABLE . ' WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select("SELECT SQL_CALC_FOUND_ROWS * FROM " . self::TABLE . " ORDER BY id DESC LIMIT $start, $itensPerPage");

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

        $results = $sql->select("
            SELECT SQL_CALC_FOUND_ROWS *
            FROM " . self::TABLE . " 
            WHERE name LIKE :search 
            ORDER BY name
            LIMIT $start, $itensPerPage;
        ",
            [ ':search' => '%' . $search . '%' ]
        );

        $resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

        return [
            'data' => $results,
            'total' => (int) $resultTotal[0]['nrtotal'],
            'pages' => ceil($resultTotal[0]['nrtotal'] / $itensPerPage),
        ];
    }
}

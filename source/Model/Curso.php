<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Curso extends Model
{
    const ERROR = 'CursoError';
    const SUCCESS = 'CursoSuccess';

    public static function listAllNamesCursos()
    {
        $sql = new Sql();
        return $sql->select("SELECT id,nome FROM cursos WHERE ensino = 'GRADUAÇÃO' ORDER BY nome ASC");
    }

    public static function listAllNamesCursosPosGraduacao()
    {
        $sql = new Sql();
        return $sql->select("SELECT id,nome FROM cursos WHERE ensino = 'PÓS-GRADUAÇÃO' ORDER BY nome ASC");
    }

    public static function listAll($limit = "LIMIT 9", $ensino = "GRADUAÇÃO")
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM cursos WHERE ensino = '{$ensino}' AND status = '1' ORDER BY id DESC {$limit}");
    }

    public function getWithSlug($slug)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM cursos WHERE slug = :slug", ['slug' => $slug]);
        $this->setData($results[0]);
    }

    /**
     * Obtêm todos os anexos de um determinado curso
     * @param [type] $id
     * @return void
     */
    public function getAnexosCurso($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM anexos_cursos WHERE curso_id = :id GROUP BY documento", ['id' => $id]);
        return $results;
    }

    public function save()
    {
        $sql = new Sql();

        $action = empty($this->getid()) ? "insert" : "update";

        $result = $sql->{$action}("cursos", $this->getValues());

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
        $results = $sql->select("SELECT * FROM cursos WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM cursos WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 12)
    {

        $args = func_get_args();
        $ensino = $args[2] ?? "";/* Modalidade de curso */

        $where = (!empty($ensino)) ? " WHERE ensino = '{$ensino}'" : "";

        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM cursos 
            {$where} 
            ORDER BY id DESC
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
            FROM cursos 
            WHERE nome LIKE :search 
            ORDER BY id DESC
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

<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class AnexoCurso extends Model
{
    public static function listAll($limit = "LIMIT 9")
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM anexos_cursos ORDER BY id DESC {$limit}");
    }

    public function save()
    {
        $sql = new Sql();
        $action = empty($this->getid()) ? "insert" : "update";
        $result = $sql->{$action}("anexos_cursos", $this->getValues());
        if ($result && !$this->getid()) $this->setid($result);
        $this->setData($this->getValues());
    }

    public function get($id): void
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM anexos_cursos WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM anexos_cursos WHERE id = :id', [":id" => $this->getid()]);
    }
}//End Classe

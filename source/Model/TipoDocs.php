<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class TipoDocs extends Model
{
    public static function listAll($limit = "LIMIT 9")
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM tipo_docs ORDER BY id DESC {$limit}");
    }

    public function save()
    {
        $sql = new Sql();
        $action = empty($this->getid()) ? "insert" : "update";
        $result = $sql->{$action}("tipo_docs", $this->getValues());
        if ($result && !$this->getid()) $this->setid($result);
        $this->setData($this->getValues());
    }

    public function get($id): void
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM tipo_docs WHERE id = :id", [":id" => $id]);
        $this->setData($results);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM tipo_docs WHERE id = :id', [":id" => $this->getid()]);
    }
}//End Classe

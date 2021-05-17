<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Convenio extends Model
{
    const ERROR = 'ConvenioError';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM convenios ORDER BY id DESC");
    }

    /**
     * Insere o convênio na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();
        $results = $sql->select(
            "CALL sp_convenios_save(:id, :company,:description, :address, :phones, :email)",
            array(
                ":id" => $this->getid(),
                ":company" => $this->getcompany(),
                ":description" => $this->getdescription(),
                ":address" => $this->getaddress(),
                ":phones" => $this->getphones(),
                ":email" => $this->getemail(),
            )
        );

        $this->setData($results[0]);
    }


    public static function setError($msg)
    {
        $_SESSION[Convenio::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Convenio::ERROR]) && $_SESSION[Convenio::ERROR]) ? $_SESSION[Convenio::ERROR] : '';
        Convenio::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Convenio::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM convenios WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM convenios WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM convenios 
            ORDER BY company
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
            FROM convenios 
            WHERE company LIKE :search 
            ORDER BY company
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

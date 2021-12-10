<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Page extends Model
{
    const ERROR = 'PageError';
    const SUCCESS = 'PageSuccess';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM pages ORDER BY id DESC");
    }

    /**
     * Insere o artigo na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();

        $action = empty($this->getid()) ? "insert" : "update";

        $result = $sql->{$action}("pages", $this->getValues());

        if ($result && !$this->getid()) $this->setid($result);

        return $this->setData($this->getValues());
    }

    /**
     * Insere página na base de dados.
     * @return void
     */
    public function save_()
    {
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_pages_save(:id,:title,:slug,:description,:qtd_access,:idperson,:author)",
            [
                ":id" => $this->getid(),
                ":title" => $this->gettitle(),
                ":slug" => $this->getslug(),
                ":description" => $this->getdescription(),
                ":qtd_access" => $this->getqtd_access(),
                ":idperson" => $this->getidperson(),
                ":author" => $this->getauthor()
            ]
        );
        $this->setData($results[0]);
    }

    public static function setError($msg)
    {
        $_SESSION[Page::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Page::ERROR]) && $_SESSION[Page::ERROR]) ? $_SESSION[Page::ERROR] : '';
        Page::clearError();
        return $msg;
    }

    public static function setSuccess($msg)
    {
        $_SESSION[Page::SUCCESS] = $msg;
    }
    public static function getSuccess()
    {
        $msg = (isset($_SESSION[Page::SUCCESS]) && $_SESSION[Page::SUCCESS]) ? $_SESSION[Page::SUCCESS] : '';
        Page::clearSuccess();
        return $msg;
    }

    public static function clearError()
    {
        $_SESSION[Page::ERROR] = NULL;
    }

    public static function clearSuccess()
    {
        $_SESSION[Page::SUCCESS] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM pages WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM pages WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 16)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM pages 
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
            FROM pages 
            WHERE title LIKE :search 
            ORDER BY id DESC
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

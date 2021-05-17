<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class ArticleCategory extends Model
{
    const ERROR = 'ArticleCategoryError';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM articles_categories ORDER BY id DESC");
    }
    /**
     * Insere o categoria na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();
        $results = $sql->select(
            "CALL sp_articles_categories_save(:id,:category)",
            [
                ":id" => $this->getid(),
                ":category" => $this->getcategory()
            ]
        );

        $this->setData($results[0]);
    }

    public static function setError($msg)
    {
        $_SESSION[ArticleCategory::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[ArticleCategory::ERROR]) && $_SESSION[ArticleCategory::ERROR]) ? $_SESSION[ArticleCategory::ERROR] : '';
        ArticleCategory::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[ArticleCategory::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM articles_categories WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM articles_categories WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM articles_categories 
            ORDER BY id
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
            FROM articles_categories 
            WHERE category LIKE :search 
            ORDER BY category
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

<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Article extends Model
{
    const ERROR = 'ArticleError';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM articles ORDER BY id DESC");
    }

    public function getWithSlug($slug)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM articles WHERE slug = :slug", ['slug' => $slug]);
        $this->setData($results[0]);
    }

    public static function listAllOneLess()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM articles ORDER BY id DESC LIMIT 1,3");
    }
    public static function firstArticle()
    {
        $sql = new Sql();
        $result = $sql->select("SELECT * FROM articles ORDER BY id DESC LIMIT 1");
        return  $result[0];
    }

    /**
     * Insere o artigo na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_articles_save(:id,:title,:description,:slug,:image,:image_thumb,:keywords,:author,:resume,:qtd_access,:spotlight,:id_articles_categories,:show_author,:idperson)",
            [
            ":id" => $this->getid(),
            ":title" => $this->gettitle(),            
            ":description" => $this->getdescription(),            
            ":slug" => $this->getslug(),            
            ":image" => $this->getimage(),            
            ":image_thumb" => $this->getimage_thumb(),            
            ":keywords" => $this->getkeywords(),            
            ":author" => $this->getauthor(),            
            ":resume" => $this->getresume(),            
            ":qtd_access" => $this->getqtd_access(),            
            ":spotlight" => $this->getspotlight(),            
            ":id_articles_categories" => $this->getid_articles_categories(),            
            ":show_author" => $this->getshow_author(),           
            ":idperson" => $this->getidperson()           
            ]
        );

        $this->setData($results[0]);
    }


    public static function setError($msg)
    {
        $_SESSION[Article::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Article::ERROR]) && $_SESSION[Article::ERROR]) ? $_SESSION[Article::ERROR] : '';
        Article::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Article::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM articles WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM articles WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM articles 
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
            FROM articles 
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

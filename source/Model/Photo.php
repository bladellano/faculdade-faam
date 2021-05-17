<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Photo extends Model
{
    const ERROR = 'PhotoError';

    public static function getCoverFromAlbum($id)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM photos_albums WHERE id_photos_cover = {$id}");
    }

    public static function getPhotosFromAlbum($id)
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM photos WHERE id_photos_albums = {$id}");
    }

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM photos ORDER BY id DESC");
    }
    /**
     * Insere registro de foto/album na base de dados.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_photos_save(:id,:title,:image,:image_thumb,:id_photos_albums)",
            [
                ":id" => $this->getid(),
                ":title" => $this->gettitle(),
                ":image" => $this->getimage(),
                ":image_thumb" => $this->getimage_thumb(),
                ":id_photos_albums" => $this->getid_photos_albums()              
            ]
        );
    }

    public static function setError($msg)
    {
        $_SESSION[Photo::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[Photo::ERROR]) && $_SESSION[Photo::ERROR]) ? $_SESSION[Photo::ERROR] : '';
        Photo::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[Photo::ERROR] = NULL;
    }

    public function get($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM photos WHERE id = :id", [":id" => $id]);
        $this->setData($results[0]);
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM photos WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
            "SELECT SQL_CALC_FOUND_ROWS *
            FROM photos 
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
            FROM photos 
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

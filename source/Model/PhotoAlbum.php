<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class PhotoAlbum extends Model
{
    const ERROR = 'PhotoAlbumError';

    public static function listAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM photos_albums ORDER BY id DESC");
    }
    /**
     * Insert the album into the database.
     * @return void
     */
    public function save()
    {
        $sql = new Sql();

        $results = $sql->select(
            "CALL sp_photos_albums_save(:id,:album,:id_photos_cover)",
            [
                ":id" => $this->getid(),
                ":album" => $this->getalbum(),                      
                ":id_photos_cover" => $this->getid_photos_cover()                      
            ]
        );

        $this->setData($results[0]);
    }

    public static function setError($msg)
    {
        $_SESSION[PhotoAlbum::ERROR] = $msg;
    }
    public static function getError()
    {
        $msg = (isset($_SESSION[PhotoAlbum::ERROR]) && $_SESSION[PhotoAlbum::ERROR]) ? $_SESSION[PhotoAlbum::ERROR] : '';
        PhotoAlbum::clearError();
        return $msg;
    }
    public static function clearError()
    {
        $_SESSION[PhotoAlbum::ERROR] = NULL;
    }

    
    public function verifyNameAlbum($album)
    {

        $sql = new Sql();
        return $sql->select("SELECT * FROM photos_albums WHERE album = :album", [":album" => $album]);
    }

    public function get($id)
    {

        $sql = new Sql();
        $results = $sql->select("SELECT * FROM photos_albums WHERE id = :id", [":id" => $id]);

        $this->setData($results[0]);
    }

    public function getPhotos($id)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM photos WHERE id_photos_albums = :id", [":id" => $id]);
        #$this->setData($results);
        return $results;
    }

    public function delete()
    {
        $sql = new Sql();
        $sql->query('DELETE FROM photos_albums WHERE id = :id', [":id" => $this->getid()]);
    }

    public static function getPage($page = 1, $itensPerPage = 8)
    {
        $start = ($page - 1) * $itensPerPage;

        $sql = new Sql();

        $results = $sql->select(
        "SELECT SQL_CALC_FOUND_ROWS *,
        (SELECT COUNT(*) FROM photos p WHERE p.id_photos_albums = pa.id) AS qtd_photos
        FROM photos_albums pa
        ORDER BY pa.id
        LIMIT $start, $itensPerPage;
        ");    

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
            "SELECT SQL_CALC_FOUND_ROWS *,
            (SELECT COUNT(*) FROM photos p WHERE p.id_photos_albums = pa.id) AS qtd_photos
            FROM photos_albums pa
            WHERE pa.album LIKE :search 
            ORDER BY pa.album
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
}//End Class

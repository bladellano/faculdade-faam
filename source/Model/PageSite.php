<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class PageSite extends Model
{

    public static function lisAll()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM pages ORDER BY id DESC LIMIT 9");
    }

    public function getWithSlug($slug)
    {
        $sql = new Sql();
        $results = $sql->select("SELECT * FROM pages WHERE slug = :slug", ['slug' => $slug]);
        if(!count($results)) return;
        $this->setData($results[0]);
    }
}

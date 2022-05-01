<?php

namespace Source\Model;

use \Source\DB\Sql;
use \Source\Model;

class Ouvidoria extends Model
{
    public static function listTipos()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM ouv_tipos");
    }

    public static function listUsuarios()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM ouv_usuarios");
    }
    public static function listSetores()
    {
        $sql = new Sql();
        return $sql->select("SELECT * FROM ouv_setores");
    }

    public static function getRespostas($q=['id'=>null])
    {
        $where = 'WHERE TRUE';

        if (isset($q['id'])) {
            $where .= ' AND res.id ='.$q['id'];
        }

        $query = "SELECT
            res.id,
            us.nome as usuario,
            tp.nome as tipo,
            tp.descricao, 
            st.nome as setor,
            res.observacao,
            res.created_at 
            FROM ouv_respostas res 
                INNER JOIN ouv_usuarios us ON us.id = res.usuario 
                INNER JOIN ouv_tipos tp ON tp.id = res.tipo  
                INNER JOIN ouv_setores st ON st.id = res.setor {$where}";

        $sql = new Sql();

        $retorno = $sql->select($query);

        $retorno = array_map(function($item){
             $item['created_at'] = toDatePtBr($item['created_at'],true);
             return $item;
        },$retorno);

        return $retorno;
    }
    
}

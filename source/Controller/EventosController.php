<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Evento;

class EventosController extends Controller
{
    public static $msgError = "Selecione uma imagem válida.";
    public static $msgSuccess = "Registro atualizado com sucesso.";
    public static $path = "storage/images";
    public static $folder = "eventos";

    public function index()
    {
        $pg = $this->pagination('Evento', '/admin/eventos');
        $page = new PageAdmin();
        $page->setTpl("eventos", array(
            "eventos" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ));
        exit;
    }

    public function changeStatus(Request $request, Response $response, array $args)
    {
        $evento = new Evento();
        $evento->get((int) $request->getParsedBody()['id']);
        $data = $evento->getValues();

        $data['status'] = ($data['status'] == 1) ? 0 : 1;

        $evento->setData($data);
        $evento->save($data);
    }


    public function changeOrder(Request $request, Response $response, array $args)
    {
        $evento = new Evento();
        $evento->get((int) $request->getParsedBody()['id']);
        $data = $evento->getValues();
        $data['in_order'] = date('Y-m-d H:i:s');
        $evento->setData($data);
        $evento->save($data);
    }


    public function create()
    {
        $page = new PageAdmin();
        $page->setTpl("eventos-create", [
            'msgError' => Evento::getError(),
            'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js']
        ]);
        exit;
    }

    public function store(Request $request, Response $response, array $args)
    {
        $data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRIPPED);

        $_SESSION['recoversPost'] = $data;

        if (in_array("", $data)  || $_FILES['image']['error'] == 4) {
            Evento::setError('Preencha os campos obrigatórios (*)');
            header("Location: /admin/eventos/create");
            exit;
        }

        if (!$this->isDate($_POST["event_day"])) {
            Evento::setError("Data do evento no formato inválido.");
            header("Location: /admin/eventos/" . $args['id']);
            exit;
        }

        /* Valida se $_FILES existem com imagem */
        if (!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {

            $images = parent::uploadImage($_FILES, self::$path, self::$folder);

            if (!count($images)) {
                Evento::setError(self::$msgError);
                header("Location: /admin/eventos/create");
                exit;
            }

            $data['image'] = $images['image'];
            $data['image_thumb'] = $images['image_thumb'];
            $data['in_order'] = date('Y-m-d H:m:s');
            $data['user_id'] = $_SESSION['User']['iduser'];
        } /* End */


        $evento = new Evento();
        $evento->setData($data);

        $evento->save();
        unset($_SESSION['recoversPost']);
        header("Location:/admin/eventos");
        exit;
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        $banner = new Evento();
        $banner->get((int) $args['id']);

        if (file_exists($banner->getimage())) {
            unlink($banner->getimage());
            unlink($banner->getimage_thumb());
        }

        $banner->delete();
        header("Location: /admin/eventos");
        exit;
    }

    public function edit(Request $request, Response $response, array $args)
    {
        $evento = new Evento();
        $evento->get((int) $args['id']);
        $page = new PageAdmin();

        $data =  $evento->getValues();
        $data['event_day'] = explode(" ", $data['event_day'])[0];

        $page->setTpl("eventos-update", [
            "evento" => $data,
            'msgError' => Evento::getError(),
            'msgSuccess' => Evento::getSuccess(),
            'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js']
        ]);
        exit;
    }

    /**
     * Verifica se o formato da data é válida para o banco.
     * @param [type] $str data a ser verificada
     * @return boolean
     */
    public function isDate($str)
    {
        $re = '/^\d{4}-\d{2}-\d{2}$/m';
        preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
        if (!$matches)
            return false;
        return true;
    }

    public function update(Request $request, Response $response, array $args)
    {

        $evento = new Evento();
        $evento->get((int) $args['id']);
        unset($_POST['_METHOD']);

        if (in_array("", $_POST)) {
            Evento::setError('Preencha os campos obrigatórios (*)');
            header("Location: /admin/eventos/" . $args['id']);
            exit;
        }

        if (!$this->isDate($_POST["event_day"])) {
            Evento::setError("Data do evento no formato inválido.");
            header("Location: /admin/eventos/" . $args['id']);
            exit;
        }

        /* Verifica se existe arquivo para ser enviado */
        if ($_FILES['image']['error'] == 0) {

            /*Primeiro apaga imagens anteriores*/
            if (file_exists($evento->getimage())) {
                unlink($evento->getimage());
                unlink($evento->getimage_thumb());
            }

            $images = parent::uploadImage($_FILES['image'], self::$path, self::$folder);

            if (is_array($images) && !count($images)) {
                Evento::setError(self::$msgError);
                header("Location: /admin/eventos/" . $args['id']);
                exit;
            }

            $_POST['image'] = $images['image'];
            $_POST['image_thumb'] = $images['image_thumb'];
        }

        $evento->setData($_POST);
        $evento->save();

        Evento::setSuccess(self::$msgSuccess);

        header("Location:/admin/eventos/" . $args['id']);
        exit;
    }
}//End Class

<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Curso;

use Slim\Http\UploadedFile;

class CursosController extends Controller
{
    public static $msgError = "Selecione uma imagem válida.";
    public static $msgSuccess = "Registro atualizado com sucesso.";
    public static $path = "storage/images";
    public static $path_files = "storage/cursos-pdfs/";
    public static $folder = "cursos";

    public function index()
    {
        $pg = $this->pagination('Curso', '/admin/cursos');
        $page = new PageAdmin();
        $page->setTpl("cursos", array(
            "cursos" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ));
        exit;
    }

    public function create()
    {
        $page = new PageAdmin();
        $turnos = ['MANHÃ', 'TARDE', 'NOITE'];
        $page->setTpl("cursos-create", [
            'msgError' => Curso::getError(),
            'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js'],
            'turnos' => $turnos
        ]);
        exit;
    }

    public function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function store(Request $request, Response $response, array $args)
    {
        $data = filter_var_array($request->getParsedBody(), FILTER_SANITIZE_STRIPPED);
        $files = $request->getUploadedFiles();

        $_SESSION['recoversPost'] = $data;

        if (in_array("", $data)) {
            Curso::setError('Preencha os campos obrigatórios (*)');
            header("Location: /admin/cursos/create");
            exit;
        }

        if (!empty($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $images = parent::uploadImage($_FILES['logo'], self::$path, self::$folder);
            if (is_array($images) && !count($images)) {
                Curso::setError(self::$msgError);
                header("Location: /admin/cursos/create");
                exit;
            }
            $data['logo'] = $images['image'];
        }

        if (!empty($_FILES['cover']) && $_FILES['cover']['error'] == 0) {
            $images = parent::uploadImage($_FILES['cover'], self::$path, self::$folder);
            if (is_array($images) && !count($images)) {
                Curso::setError(self::$msgError);
                header("Location: /admin/cursos/create");
                exit;
            }
            $data['cover'] = $images['image'];
        }

        $curso = new Curso();
        $curso->setData($data);
        $curso->save();

        /**
         * TRATA OS ARQUIVOS PDFS HOME DO CURSO
         */
        $id = $curso->getValues()['id'];

        $allowedTypeDocs = [
            'matriz_curricular',
            'portaria',
            'biblioteca',
            'manual_docente',
            'manual_discente'
        ];

        foreach ($files as $key => $file) {

            if (in_array($key, $allowedTypeDocs) && !$file->getError()) {

                $filename = $this->moveUploadedFile(self::$path_files, $file);

                $data_doc = [
                    'curso_id' => $id,
                    'tipo_doc' => $key,
                    'documento' => $file->getClientFilename(),
                    'arquivo' => self::$path_files . $filename
                ];

                $anexo = new \Source\Model\AnexoCurso;
                $anexo->setData($data_doc);
                $anexo->save();
            }
        }
        /**
         * FIM - TRATAMENTO DOS PDFS
         */

        unset($_SESSION['recoversPost']);
        header("Location:/admin/cursos");
        exit;
    }

    public function destroy(Request $request, Response $response, array $args)
    {
        $banner = new Curso();
        $banner->get((int) $args['id']);

        if (file_exists($banner->getimage())) {
            unlink($banner->getimage());
            unlink($banner->getimage_thumb());
        }

        $banner->delete();
        header("Location: /admin/cursos");
        exit;
    }

    public function edit(Request $request, Response $response, array $args)
    {
        
        $curso = new Curso();
        $curso->get((int) $args['id']);

        $page = new PageAdmin();

        $data = $curso->getValues();

        $turnos = ['MANHÃ', 'TARDE', 'NOITE'];

        $page->setTpl("cursos-update", [
            "curso" => $data,
            'msgError' => Curso::getError(),
            'msgSuccess' => Curso::getSuccess(),
            'turnos' => $turnos,
            'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js']
        ]);
        exit;
    }

    public function update(Request $request, Response $response, array $args)
    {

        $curso = new Curso();
        $curso->get((int) $args['id']);
        unset($_POST['_METHOD']);

        if (in_array("", $_POST)) {
            Curso::setError('Preencha os campos obrigatórios (*)');
            header("Location: /admin/eventos/" . $args['id']);
            exit;
        }

        /* Verifica se existe arquivo para ser enviado */
        if ($_FILES['image']['error'] == 0) {

            /*Primeiro apaga imagens anteriores*/
            if (file_exists($curso->getimage())) {
                unlink($curso->getimage());
                unlink($curso->getimage_thumb());
            }

            $images = parent::uploadImage($_FILES, self::$path, self::$folder);

            if (is_array($images) && !count($images)) {
                Curso::setError(self::$msgError);
                header("Location: /admin/eventos/" . $args['id']);
                exit;
            }

            $_POST['image'] = $images['image'];
            $_POST['image_thumb'] = $images['image_thumb'];
        }

        $curso->setData($_POST);
        $curso->save();

        Curso::setSuccess(self::$msgSuccess);

        header("Location:/admin/cursos/" . $args['id']);
        exit;
    }
}//End Class

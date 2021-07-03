<?php

namespace Source\Controller;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Source\PageAdmin;
use Source\Model\Curso;

use Slim\Http\UploadedFile;
use Source\Model\AnexoCurso;
use Source\Model\TipoDocs;

class CursosController extends Controller
{
    private static $msgError = "Selecione uma imagem válida.";
    private static $msgSuccess = "Registro atualizado com sucesso";
    private static $path = "storage/images";
    private static $path_files = "storage/cursos-pdfs/";
    private static $folder = "cursos";
    private static $aTypeImages = ['cover', 'logo'];
    private static $turnos = ['MANHÃ', 'TARDE', 'NOITE', 'TARDE/NOITE'];
    private static $ensinos = ['GRADUAÇÃO', 'PÓS-GRADUAÇÃO'];

    public function index()
    {
        $pg = $this->pagination('Curso', '/admin/cursos','GRADUAÇÃO');
        $page = new PageAdmin();
        $page->setTpl("cursos", array(
            "cursos" => $pg['data'],
            "search" => $pg['search'],
            "pages" => $pg['pages']
        ));
        exit;
    }

    public function showListPosGraduacao()
    {
        $pg = $this->pagination('Curso', '/admin/cursos', 'PÓS-GRADUACAO');
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
        $turnos = self::$turnos;
        $ensinos = self::$ensinos;
        $tipoDocs = $this->getTypeDocuments();

        $page->setTpl("cursos-create", [
            'msgError' => Curso::getError(),
            'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js'],
            'turnos' => $turnos,
            'ensinos' => $ensinos,
            'tipo_docs' => $tipoDocs
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

        if (empty($data["nome"]) || empty($data["ensino"])) {
            Curso::setError('Preencha os campos obrigatórios (*)');
            header("Location: /admin/cursos/create");
            exit;
        }

        foreach (self::$aTypeImages as $type) {

            if (!empty($_FILES[$type]) && $_FILES[$type]['error'] == 0) {
                $images = parent::uploadImage($_FILES[$type], self::$path, self::$folder);
                if (is_array($images) && !count($images)) {
                    Curso::setError(self::$msgError);
                    header("Location: /admin/cursos/" . $args['id']);
                    exit;
                }
                $data[$type] = $images['image'];
            }
        }

        $curso = new Curso();
        $curso->setData($data);
        $curso->save();

        /**
         * TRATA OS ARQUIVOS PDFS HOME DO CURSO
         */
        $id = $curso->getValues()['id'];

        $allowedTypeDocs = $this->getTypeDocuments();

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

    public function deleteDoc(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();
        $oAnexo = (new AnexoCurso());
        $oAnexo->getById($data['id']);
        $oAnexo->delete();

        if (file_exists($oAnexo->getValues()['arquivo'])) {
            @unlink($oAnexo->getValues()['arquivo']);
            die(json_encode(["success" => true]));
        } else {
            die(json_encode(["success" => false]));
        }
    }

    public function edit(Request $request, Response $response, array $args)
    {

        $curso = new Curso();
        $curso->get((int) $args['id']);

        $page = new PageAdmin([
            // "header" => false,
            // "footer" => false,
        ]);

        $data = $curso->getValues();

        $oAnexosCurso = new AnexoCurso();
        $oAnexosCurso->get((int) $args['id']);

        $aAnexosCursos = $oAnexosCurso->getValues();

        /* Modifica por referência o array de anexos */
        foreach ($aAnexosCursos as $key => &$anexo) {
            $aAnexosCursos[$anexo['tipo_doc']] = $anexo;
            if (is_int($key)) unset($aAnexosCursos[$key]);
        }

        $turnos = self::$turnos;

        /* Busca no banco os tipos de doc existentes */
        $tipoDocs = $this->getTypeDocuments();

        /* Monta o array para listar no front com os anexos possíveis */
        $anexosFront = [];
        foreach ($tipoDocs as $key) {
            if (in_array($key, array_keys($aAnexosCursos))) :
                $anexosFront[$key] = $aAnexosCursos[$key];
            else :
                $anexosFront[$key] = [];
            endif;
        }

        $page->setTpl("cursos-update", [
            "curso" => $data,
            'msgError' => Curso::getError(),
            'msgSuccess' => Curso::getSuccess(),
            'anexos' => $anexosFront,
            'turnos' => $turnos,
            'scripts' => ['https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js', '/views/admin/assets/js/form.js']
        ]);
        exit;
    }

    private function getTypeDocuments()
    {
        $tipoDocs = (new TipoDocs)->listAll();
        foreach ($tipoDocs as &$tipo)
            $tipoDocs[$tipo] = $tipo['tipo'];

        $tipoDocs = array_map(function ($t) {
            return $t['tipo'];
        }, $tipoDocs);

        return $tipoDocs;
    }

    public function update(Request $request, Response $response, array $args)
    {
        $data = $request->getParsedBody();
        $files = $request->getUploadedFiles();

        $curso = new Curso();
        $curso->get((int)$args['id']);
        unset($data['_METHOD']);

        /**
         * TRATA OS ARQUIVOS PDFS HOME DO CURSO
         */
        $id = $curso->getValues()['id'];

        $allowedTypeDocs = $this->getTypeDocuments();

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

        foreach (self::$aTypeImages as $type) {

            if (!empty($_FILES[$type]) && $_FILES[$type]['error'] == 0) {
                $images = parent::uploadImage($_FILES[$type], self::$path, self::$folder);
                if (is_array($images) && !count($images)) {
                    Curso::setError(self::$msgError);
                    header("Location: /admin/cursos/" . $args['id']);
                    exit;
                }
                @unlink($curso->getValues()[$type]);
                $data[$type] = $images['image'];
            }
        }

        if (empty($data["nome"]) || empty($data["ensino"])) {
            Curso::setError('Preencha os campos obrigatórios (*)');
            header("Location: /admin/cursos/" . $args['id']);
            exit;
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $curso->setData($data);
        $curso->save();

        Curso::setSuccess(self::$msgSuccess);

        header("Location:/admin/cursos/" . $args['id']);
        exit;
    }
}//End Class

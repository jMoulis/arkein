<?php

namespace DocumentationBundle\Controller;

use AppBundle\Api\FileSystemApiModel;
use AppBundle\Controller\BaseController;
use DocumentationBundle\Form\FileFormType;
use DocumentationBundle\Form\FolderType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("filesystem")
 */

class FileSystemController extends BaseController
{

    /**
     * @Route("/api/fileSystem/folder/new",
     *     name="api_new_folder",
     *     options={"expose" = true}
     *  )
     */
    public function createFolderAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(FolderType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        $fs = new Filesystem();
        $baseDir = $this->get('kernel')->getRootDir().'/../src/documents';

        try {
            $fs->mkdir($baseDir.'/'.$data['name']);

            $finder = new Finder();
            $finder->directories()->name($data['name']);

            foreach ($finder->in($baseDir) as $folder)
            {
                $apiModel = $this->createFolderApiModel($folder);
            }

            $response = $this->createApiResponse($apiModel);
            // setting the Location header... it's a best-practice

            return $response;

        } catch (IOExceptionInterface $e) {

            return "An error occurred while creating your directory at ".$e->getPath();
        }

    }

    /**
     * @Route("/api/fileSystem/new",
     *     name="api_new_file",
     *     options={"expose" = true}
     *  )
     * @Method("POST")
     */
    public function createFileAction(Request $request)
    {
        $data = $request->files;
        $folder = $request->request->get('folder');

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(FileFormType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        $baseDir = $folder.'/';
        $file = $data->get('file');
        $fileName = md5($file->getClientOriginalName()) . '.' . $file->guessExtension();
        $file->move($baseDir, $fileName);

        $json = [];
        $json[] = $fileName;

        return new JsonResponse(['data' => $json]);
    }

    /**
     * @Route("/api/fileSystem/folders",
     *     name="api_get_folders",
     *     options={"expose" = true}
     *  )
     */
    public function getFoldersAndFilesAction()
    {
        $userFolder = 'documents';
        $baseDir = $this->get('kernel')->getRootDir().'/../src/'.$userFolder;

        $finder = new Finder();
        $finder->directories()->in($baseDir);

        $models = [];

        foreach ($finder as $folder) {
            $models[] = $this->createFolderApiModel($folder);
        }

        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     *
     * @return FileSystemApiModel
     * @Security("has_role('ROLE_ADMIN')")
     */
    private function createFolderApiModel($folder)
    {
        $model = new FileSystemApiModel();
        $model->folderId = $folder->getFilename();
        $model->folderName = $folder->getFilename();
        $model->folderPath = $folder->getRealPath();

        $fileFinder = new Finder();
        $fileFinder->files()->in($folder->getRealPath());
        foreach ($fileFinder as $file) {
            $model->files[] = [
                'folder_id' => $folder->getFilename(),
                'id' => $file->getFilename(),
                'name' => $file->getFilename(),
                'extension' => $file->getExtension(),
                'info' => $file->getFileInfo()
            ];
        }
        return $model;
    }


}

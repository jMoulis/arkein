<?php
/**
 * Created by PhpStorm.
 * User: julienmoulis
 * Date: 23/05/2017
 * Time: 13:02
 */

namespace AppBundle\Service;

use AppBundle\Controller\BaseController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ajaxResponse
{

    /**
     * @var FormFactoryInterface
     */
    private $factory;
    /**
     * @var BaseController
     */
    private $baseController;

    public function __construct(FormFactoryInterface $factory, BaseController $baseController)
    {
        $this->factory = $factory;
        $this->baseController = $baseController;
    }

    /**
     * Get the validation api answer
     */
    public function ajaxResponse($formType, $data)
    {
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->factory->create($formType, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $this->baseController->getErrorsFromForm($form);

            return $this->baseController->createApiResponse([
                'errors' => $errors
            ], 400);
        }
        return $form;
    }

}

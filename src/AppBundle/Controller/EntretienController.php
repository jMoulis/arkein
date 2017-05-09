<?php

namespace AppBundle\Controller;

use AppBundle\Api\EntretienApiModel;
use AppBundle\Entity\Entretien;
use AppBundle\Entity\InterviewUser;
use AppBundle\Form\EntretienType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use UserBundle\Entity\User;

/**
 * Entretien controller.
 *
 * @Route("entretien")
 */
class EntretienController extends BaseController
{
    /**
     * Lists all entretien entities par interviewee.
     *
     * @Route("/", name="entretien_index", options={"expose" = true})
     * @Method("GET")
     *
     */
    public function indexAction(Request $request, User $user = null)
    {
        $em = $this->getDoctrine()->getManager();

        $entretiens = $em->getRepository('AppBundle:Entretien')->findBy([
            'author' => $this->getUser()
        ]);

        return $this->render('entretien/_interviews-by-author.html.twig', array(
            'entretiens' => $entretiens,
        ));
    }

    /**
     * @Route("/new/", name="page_new")
     */
    public function newPageAction()
    {
        return $this->render('entretien/new.html.twig');
    }

    /**
     * Creates a new entretien entity.
     *
     * @Route("/api/new/", name="entretien_new", options={"expose" = true})
     * @Method({"GET", "POST"})
     *
     * Un pb s'est posé à moi, il était impossible à ma connaissance de récupérer
     * via un select classique les données pour créer des guests et les persister
     * Je devais donc récupérer les données formulaire et les données d'un objet dans lequel
     * je récupèrerai tous les invités dans un tableau et ferai un loop pour ajouter les guests en bdd
     * Voir l'API Javascript EntretienAPP pour le détail js et le entretien/new.html.twig pour la gestion des li
     */
    public function newAction(Request $request)
    {
        /*TODO Mettre en place le workflow mail et l'outil de validation des présences*/
        /* $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');*/
        $data = json_decode($request->getContent(), true);
        $dataFormEntretien = $data[0];
        $newGuests = $data[1];

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(EntretienType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($dataFormEntretien);

        if (!$form->isValid()) {

            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        /** @var Entretien $entretien */
        $entretien = $form->getData();

        $em = $this->getDoctrine()->getManager();
        $entretien->setAuthor($this->getUser());

        if ($newGuests) {
            foreach ($newGuests as $guest) {
                $interviewUser = new InterviewUser();
                $interviewUser->setUser($em->getRepository('UserBundle:User')->find($guest));
                $interviewUser->setInterview($entretien);
                $entretien->addInterviewGuest($interviewUser);
            }
        }
        $em->persist($entretien);
        $em->flush();

        $apiModel = $this->createEntretienApiModel($entretien);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('entretien_show', ['id' => $entretien->getId()])
        );

        return $response;
    }



    /**
     * @Route("/api/guest/{id}/", name="entretien_list_by_invitation", options={"expose" = true})
     * @Method("GET")
     */
    public function getInvitationAction(User $user)
    {
        if(!$user) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $entetiens = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->getInterviewByGuest($user)
        ;
        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/api/author/{id}/", name="entretien_list_by_author", options={"expose" = true})
     * @Method("GET")
     */
    public function getEntretiensByAuthorAction(User $user)
    {
        if(!$user) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $entetiens = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->findBy([
                'author' => $user
            ])
        ;
        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * @Route("/api/young/{id}/", name="entretien_list_by_young", options={"expose" = true})
     * @Method("GET")
     *
     * This is for the loading entretiens in the show user
     */
    public function getEntretiensByYoungAction(User $user)
    {
        if(!$user) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $entetiens = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->findBy([
                'young' => $user
            ])
        ;
        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }
        return $this->createApiResponse([
            'items' => $models
        ]);
    }

    /**
     * Finds and displays a entretien entity.
     *
     * @Route("/api/modal/{id}/show",
     *     name="entretien_modal_detail",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function detailModalAction(Entretien $entretien)
    {
        if(!$entretien) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $entetien = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->find($entretien->getId())
        ;
        $model = $this->createEntretienApiModel($entetien);
        return $this->createApiResponse([
            'item' => $model
        ]);
    }

    /**
     * Finds and displays a entretien entity.
     *
     * @Route("/{id}/show",
     *     name="entretien_show",
     *     options={"expose" = true})
     * @Method("GET")
     */
    public function showAction(Entretien $entretien)
    {
        return $this->render('entretien/show.html.twig', array(
            'entretien' => $entretien
        ));
    }

    /**
     * Displays a form to edit an existing entretien entity.
     *
     * @Route("/{id}/edit", name="entretien_edit",
     *     options={"expose" = true})
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request)
    {
        $entretienId = $request->attributes->get('id');

        $data = json_decode($request->getContent(), true);

        // First array that retreive the sumbitted form
        // Second array that retreive the guests
        $dataFormEntretien = $data[0];
        $newGuests = $data[1];

        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(EntretienType::class, null, [
            'csrf_protection' => false,
        ]);

        $form->submit($dataFormEntretien);

        if (!$form->isValid()) {

            $errors = $this->getErrorsFromForm($form);

            return $this->createApiResponse([
                'errors' => $errors
            ], 400);
        }

        $em = $this->getDoctrine()->getManager();
        $entretien = $em->getRepository('AppBundle:Entretien')->findOneBy(['id' => $entretienId]);

        /*
         * This function's role is to:
         * 1st: Retreive the guest already invited - $existingGuests
         * 2nd: Retreive the guest submitted by the form - $newGuest
         *
         * Then it compares the two arrays twice:
         * 1st: Check the guests to be remove
         * 2nd: Check the guests to add
         * Then
         * */
        $existingGuests = [];
        foreach ($entretien->getInterviewGuests() as $interviewGuest){
            $existingGuests[] = $interviewGuest->getId();
        };
        $guestsToRemove = array_diff($existingGuests, $newGuests);
        $guestsToAdd = array_diff($newGuests, $existingGuests);

        $entretien->setObjet($dataFormEntretien['objet']);
        $entretien->setCompteRendu($dataFormEntretien['compteRendu']);



        if(!empty($newGuests)){
            /*
             * I add guests
             * */
            foreach ($guestsToAdd as $guest) {
                $interviewUser = new InterviewUser();
                $interviewUser->setUser($em->getRepository('UserBundle:User')->find($guest));

                $interviewUser->setInterview($entretien);
                $entretien->addInterviewGuest($interviewUser);
            }
            /* Then remove
             *
             * */
            foreach ($guestsToRemove as $removeInterviewGuest)
            {
                $interviewGuest = $em->getRepository('AppBundle:InterviewUser')->find($removeInterviewGuest);
                $entretien->removeInterviewGuest( $interviewGuest);
            }
        } else {
            /*
             * I remove all guests
             * */
            foreach ($guestsToRemove as $removeInterviewGuest)
            {
                $interviewGuest = $em->getRepository('AppBundle:InterviewUser')->find($removeInterviewGuest);
                $entretien->removeInterviewGuest( $interviewGuest);
            }
        }

        $date = new \DateTime(($dataFormEntretien['date']));


        $entretien->setDate($date);

        $entretien->getYoung($dataFormEntretien['young']);

        $em->persist($entretien);
        $em->flush();

        $apiModel = $this->createEntretienApiModel($entretien);

        $response = $this->createApiResponse($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('entretien_show', ['id' => $entretien->getId()])
        );

        return $response;

    }

    /**
     * Deletes a entretien entity.
     *
     * @Route("/{id}", name="entretien_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, Entretien $entretien)
    {
        $form = $this->createDeleteForm($entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entretien);
            $em->flush();
        }

        return $this->redirectToRoute('entretien_index');
    }

    /**
     * Creates a form to delete a entretien entity.
     *
     * @param Entretien $entretien The entretien entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Entretien $entretien)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('entretien_delete', array('id' => $entretien->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function createEntretienApiModel(Entretien $entretien)
    {
        $model = new EntretienApiModel();
        $model->id = $entretien->getId();
        $model->compteRendu = $entretien->getCompteRendu();
        $model->objet = $entretien->getObjet();
        $model->date = $entretien->getDate()->format('d/m/Y');
        foreach ($entretien->getInterviewGuests() as $interviewGuest) {
            $model->guests[] = [
                'name' => $interviewGuest->__toString(),
                'id' => $interviewGuest->getId(),
                'status' => $interviewGuest->getStatus()
            ];
        }
        $model->author = $entretien->getAuthor()->__toString();
        $model->authorId = $entretien->getAuthor()->getId();
        $model->young = $entretien->getYoung()->__toString();
        $model->youngId = $entretien->getYoung()->getId();

        $selfUrl = $this->generateUrl(
            'entretien_show',
            ['id' => $entretien->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }

}

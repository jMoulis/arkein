<?php

namespace AppBundle\Controller;

use AppBundle\Api\EntretienApiModel;
use AppBundle\Entity\Entretien;
use AppBundle\Entity\InterviewUser;
use AppBundle\Form\Type\EntretienType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
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
    public function indexAction()
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
        $data = json_decode($request->getContent(), true);
        $dataFormEntretien = $data[0];
        $newGuests = $data[1];

        $form = $this->get('app.api_response')->ajaxResponse(EntretienType::class, $dataFormEntretien);

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

        $response = $this->createApiResponseAction($apiModel);
        $response->headers->set(
            'Location',
            $this->generateUrl('entretien_show', ['id' => $entretien->getId()])
        );

        return $response;
    }

    /**
     * Finds and displays a entretien entity.
     *
     * @Route("/{id}/show",
     *     name="entretien_show",
     *     options={"expose" = true}
     * )
     *
     * @Method("GET")
     */
    public function showAction(Entretien $entretien)
    {
        return $this->render('entretien/show.html.twig', array(
            'entretien' => $entretien
        ));
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
            ->getInterviewByGuest($user);
        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }
        return $this->createApiResponseAction([
            'items' => $models
        ]);
    }

    /**
     * @Route("/api/author/{id}/",
     *     name="entretien_list_by_author",
     *     options={"expose" = true}
     * )
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
            ]);
        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }
        return $this->createApiResponseAction([
            'items' => $models
        ]);
    }

    /**
     * @Route("/api/young/{id}/",
     *     name="entretien_list_by_young",
     *     options={"expose" = true}
     * )
     *
     * @Method("GET")
     *
     * Load entretiens in the show user
     */
    public function getEntretiensByYoungAction(User $user)
    {
        if(!$user) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $entetiens = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->findBy([
                'young' => $user
            ]);
        $models = [];
        foreach ($entetiens as $entetien) {
            $models[] = $this->createEntretienApiModel($entetien);
        }
        return $this->createApiResponseAction([
            'items' => $models
        ]);
    }

    /**
     * Finds and displays a entretien entity.
     *
     * @Route("/api/modal/{id}/show",
     *     name="entretien_modal_detail",
     *     options={"expose" = true}
     * )
     *
     * @Method("GET")
     */
    public function detailModalAction(Entretien $entretien)
    {
        if(!$entretien) {
            throw new \Exception('erreur object non trouvé', 500);
        }
        $entetien = $this->getDoctrine()->getRepository('AppBundle:Entretien')
            ->find($entretien->getId());
        $model = $this->createEntretienApiModel($entetien);
        return $this->createApiResponseAction([
            'item' => $model
        ]);
    }

    /**
     * Displays a form to edit an existing entretien entity.
     *
     * @Route("/{id}/edit",
     *     name="entretien_edit",
     *     options={"expose" = true}
     * )
     *
     * @Method({"GET", "POST"})
     *
     */
    public function editAction(Request $request)
    {
        $entretienId = $request->attributes->get('id');
        $data = json_decode($request->getContent(), true);
        $dataFormEntretien = $data[0];

        $this->get('app.api_response')->ajaxResponse(EntretienType::class, $dataFormEntretien);

        $em = $this->getDoctrine()->getManager();
        $entretien = $em->getRepository('AppBundle:Entretien')->findOneBy(['id' => $entretienId]);

        $this->guestManager($entretien, $data, $dataFormEntretien, $em);
        $date = new \DateTime(($dataFormEntretien['date']));
        $entretien->setDate($date);

        $em->persist($entretien);
        $em->flush();

        $apiModel = $this->createEntretienApiModel($entretien);

        $response = $this->createApiResponseAction($apiModel);
        // setting the Location header... it's a best-practice
        $response->headers->set(
            'Location',
            $this->generateUrl('entretien_show', ['id' => $entretien->getId()])
        );

        return $response;

    }

    /**
     * This function's role is to:
     * 1st: Retreive the guest already invited - $existingGuests
     * 2nd: Retreive the guest submitted by the form - $newGuest
     *
     * Then it compares the two arrays twice:
     * 1st: Check the guests to be remove
     * 2nd: Check the guests to add
     *
     */
    private function guestManager($entity, $data, $data2, $em)
    {
        $newGuests = $data[1];
        $existingGuests = [];
        foreach ($entity->getInterviewGuests() as $interviewGuest){
            $existingGuests[] = $interviewGuest->getId();
        };
        $guestsToRemove = array_diff($existingGuests, $newGuests);
        $guestsToAdd = array_diff($newGuests, $existingGuests);

        $entity->setObjet($data2['objet']);
        $entity->setOdj($data2['odj']);

        if(!empty($newGuests)){
            foreach ($guestsToAdd as $guest) {
                $interviewUser = new InterviewUser();
                $interviewUser->setUser($em->getRepository('UserBundle:User')->find($guest));

                $interviewUser->setInterview($entity);
                $entity->addInterviewGuest($interviewUser);
            }
            foreach ($guestsToRemove as $removeInterviewGuest)
            {
                $interviewGuest = $em->getRepository('AppBundle:InterviewUser')->find($removeInterviewGuest);
                $entity->removeInterviewGuest( $interviewGuest);
            }
        } else {
            foreach ($guestsToRemove as $removeInterviewGuest)
            {
                $interviewGuest = $em->getRepository('AppBundle:InterviewUser')->find($removeInterviewGuest);
                $entity->removeInterviewGuest( $interviewGuest);
            }
        }
    }

    private function createEntretienApiModel(Entretien $entretien)
    {
        $model = new EntretienApiModel();
        $model->id = $entretien->getId();
        $model->objet = $entretien->getObjet();
        $model->date = $entretien->getDate()->format('d/m/Y');
        $model->odj = $entretien->getOdj();
        foreach ($entretien->getInterviewGuests() as $interviewGuest) {
            $model->guests[] = [
                'name' => $interviewGuest->getUser()->getFullName(),
                'id' => $interviewGuest->getId(),
                'status' => $interviewGuest->getStatus()
            ];
        }
        $model->author = $entretien->getAuthor()->getFullName();
        $model->authorId = $entretien->getAuthor()->getId();
        $model->young = $entretien->getYoung()->getFullName();
        $model->youngId = $entretien->getYoung()->getId();
        if($entretien->getCompteRendu()){
            $model->compteRendu = $entretien->getCompteRendu()->getId();
            $model->compteRenduLien = $entretien->getCompteRendu()->getLienpdf();
        }

        $selfUrl = $this->generateUrl(
            'entretien_show',
            ['id' => $entretien->getId()]
        );
        $model->addLink('_self', $selfUrl);

        return $model;
    }
}

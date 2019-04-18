<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EventFormType;
use App\Repository\EvenementRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @Route("/event", name="event")
     * @param EvenementRepository $repository
     * @return Response
     */
    public function eventList(EvenementRepository $repository)
    {
        $events = $repository->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    /**
     * @Route("/event/{id}", name="eventSingle", requirements={"id"= "\d+"})
     *
     * @param Evenement $event
     * @return Response
     */
    public function eventShow(Evenement $event)
    {
        return $this->render('event/show.html.twig', [
            'event' => $event
        ]);
    }

    /**
     * @Route("/event/{id}/delete", name="eventDelete")
     *
     * @param Evenement $event
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function eventDelete(Evenement $event, Request $request, ObjectManager $manager) {
        if($this->isCsrfTokenValid('delete'.$event->getId(), $request->get('_token'))){
            $manager->remove($event);
            $manager->flush();
        }

        return $this->redirectToRoute('event');
    }

    /**
     * @Route("/event/create", name="eventCreate")
     * @Route("/event/{id}/edit", name="eventEdit", requirements={"id"="\d+"})
     * @param Request $request
     * @param ObjectManager $em
     * @param Evenement|null $event
     * @return Response
     */
    public function eventForm(Request $request, ObjectManager $em, Evenement $event = null) {
        if(!$event){
            $event = new Evenement();
        }
        $form = $this->createForm(EventFormType::class, $event);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if ($event->getCategory() == ''){
                $event->setCategory('Sport');
            }
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('event');
        }

        return $this->render('event/index.html.twig', [
            'eventForm' => $form->createView(),
            'editMode' => $event->getId() !== null
        ]);
    }
}

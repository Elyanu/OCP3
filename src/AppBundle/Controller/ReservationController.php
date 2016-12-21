<?php

namespace AppBundle\Controller;

use AppBundle\Form\CommandeType;
use AppBundle\Form\IndexType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Class ReservationController
 * @package AppBundle\Controller
 */
class ReservationController extends Controller
{
    /**
     * @Route("/billetterie", name="billetterie")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(IndexType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $session = $request->cookies->get('ocprojet3');
            $data = $form->getData();
            $commande = $this->get('reservation.service')->createCommande($session, $data['day'], $data['dureeVisite'], $data['email']);
            if ($commande) {
                return $this->redirectToRoute('order', array(
                    'id' => $commande->getId(),
                ));
            }
            return $this->render('reservation/index.html.twig', array(
                'form' => $form->createView(),
                'error' => 'Le billet journée n\'est plus disponible après 14h.'
            ));
        }
        return $this->render('reservation/index.html.twig', array(
            'form' => $form->createView(),
            'error' => false
        ));
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/order/{id}", name="order")
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function orderAction(Request $request, $id)
    {
        if ($this->get('reservation.service')->getSession($id) === $request->cookies->get('ocprojet3'))
        {
            $form = $this->createForm(CommandeType::class);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $visitors = $data['visitors'];
                foreach ($visitors as $visitor) {
                    $this->get('reservation.service')->createBillet($id, $visitor['prenom'], $visitor['nomf'], $visitor['pays'], $visitor['dateNaissance'], $visitor['tarifReduit']);
                }
                return $this->redirectToRoute('checkout', array (
                    'id' => $id
                ));
            }
            return $this->render('reservation/order.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @param $id
     * @param Request $request
     * @Route("/checkout/{id}", name="checkout", schemes={"%secure_channel%"})
     * @Method({"GET", "POST"})
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function checkoutAction(Request $request, $id)
    {
        if ($this->get('reservation.service')->getSession($id) === $request->cookies->get('ocprojet3')) {
            $commande = $this->get('reservation.service')->getCommande($id);
            $token = $request->request->get('stripeToken');
            $error = false;
            if ($request->isMethod('POST')) {
                try {
                    $this->get('stripe.service')->createCharge($commande->getMontant(), $token, $commande->getEmail());
                }
                catch (\Stripe\Error\Card $e) {
                    $error = 'Un problème est survenu : '.$e->getMessage();
                }
                if (!$error) {
                    $this->get('reservation.service')->changeStatutTransaction($commande->getId());
                    $this->addFlash('success', 'Nous vous remercions pour votre commande, vous recevrez vos billets par e-mail');
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Votre réservation pour le musée du Louvre')
                        ->setFrom('reservation@louvre.fr')
                        ->setTo($commande->getEmail())
                        ->setBody(
                            $this->renderView(
                                'email/reservation.html.twig',
                                array(
                                    'commande' => $commande,
                                    'billets' => $commande->getBillets()
                                )
                            ),
                            'text/html'
                        );
                    $this->get('mailer')->send($message);
                    return $this->redirectToRoute('index');
                }
            }
            if ($this->get('reservation.service')->getNbrBillets($commande->getDateVisite()) + $this->get('reservation.service')->billetsCommande($id) <= 1000) {
                return $this->render('reservation/checkout.html.twig', array(
                    'id' => $id,
                    'billets' => $commande->getBillets(),
                    'visite' => $commande->getdateVisite(),
                    'email' => $commande->getEmail(),
                    'montant' => $this->get('reservation.service')->getMontant($id),
                    'stripe_public_key' => $this->getParameter('stripe_public_key'),
                    'error' => $error
                ));
            }
            throw new ConflictHttpException("Nous n'avons plus assez de places disponibles à la réservation pour la date choisie");
        }
        return $this->redirectToRoute('index');
    }

    /**
     * @param $billetId
     * @param $id
     * @Route("/remove/{id}/{billetId}", name="remove")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeAction($billetId, $id) {
        $this->get('reservation.service')->removeBillet($billetId);
        return $this->redirectToRoute('checkout', array (
            'id' => $id
        ));
    }
}



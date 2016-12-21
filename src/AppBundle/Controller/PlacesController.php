<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlacesController extends Controller
{
    /**
     * @param Request $request
     * @Route("/places", name="places_dispo")
     * @Method("GET")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function billetsDisposAction(Request $request)
    {
        $day = $request->query->get('day');
        $tickets = $this->get('reservation.service')->getNombreBillets($day);
        if ($request->isXmlHttpRequest()) {
            return new Response(1000-$tickets);
        }
        return new Response('Problème!', 400);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


class RatesController extends AbstractController
{
    /**
     * Renders the table for all the exchange rates in the database
     * @param PaginatorInterface $paginator
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $em    = $this->getDoctrine()->getManager();
        $dql   = "SELECT r FROM App\Entity\Rates r";
        $query = $em->createQuery($dql);
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), /*page number*/
            10
        );
        return $this->render('rates/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'RatesController'
        ]);
    }
}

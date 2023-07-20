<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    /**
     * This function is used to display the list of ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingrédients = $paginator->paginate(
        $repository->findAll(),
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/
    );

        return $this->render('pages/ingredient/index.html.twig', [ 'ingredients' => $ingrédients]);
    }
}

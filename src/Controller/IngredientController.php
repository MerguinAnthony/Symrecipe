<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{
    /**
     * This controller is used to display the list of ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', 'ingredient.index', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingrédients = $paginator->paginate(
        $repository->findAll(),
        $request->query->getInt('page', 1), /*page number*/
        10 /*limit per page*/
    );

        return $this->render('pages/ingredient/index.html.twig', [ 'ingredients' => $ingrédients]);
    }

    /**
     * This controller show a form which create an ingredient
     *
     * @param Ingredient $ingredient
     * @return Response
     */

    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager) : Response
    {
        $ingrédient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingrédient);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $ingrédient = $form->getData();
            $manager->persist($ingrédient);
            $manager->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été Crée avec succès !');

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This controller show a form which edit an ingredient
     * 
     * @param IngredientRepository $repository
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(IngredientRepository $repository, int $id, Request $request, EntityManagerInterface $manager) : Response
    {
        $ingredient = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $ingrédient = $form->getData();
            $manager->persist($ingrédient);
            $manager->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été modifé avec succès !');

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, int $id) : Response
    {

        if(!$id)
        {
            $this->addFlash('success', 'Votre ingrédient n\'existe pas !');
        }
        $manager->remove($manager->getRepository(Ingredient::class)->findOneBy(['id' => $id]));
        $manager->flush();

        $this->addFlash('success', 'Votre ingrédient a bien été supprimé avec succès !');

        return $this->redirectToRoute('ingredient.index');
    }
}

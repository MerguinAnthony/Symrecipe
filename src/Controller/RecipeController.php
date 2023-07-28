<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This contrôleur display All recipes
     *
     * @param RecipeRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * this controller allow us create a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $recipe =$form->getData();
            $manager->persist($recipe);
            $manager->flush();
            $this->addFlash('success', 'La recette a bien été ajoutée');
            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
         * This controller show a form which edit an recipe
         *
         * @param IngredientRepository $repository
         * @param int $id
         * @param Request $request
         * @param EntityManagerInterface $manager
         * @return Response
         */
    #[Route('/recipe/edition/{id}', 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(RecipeRepository $repository, int $id, Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = $repository->findOneBy(['id' => $id]);
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'Votre recette a bien été modifé avec succès !');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
        /**
         * This controller delete an ingredient
         *
         * @param EntityManagerInterface $manager
         * @param int $id
         * @return Response
         */
        #[Route('/recette/suppression/{id}', 'recipe.delete', methods: ['GET'])]
        public function delete(EntityManagerInterface $manager, int $id) : Response
        {

            if(!$id)
            {
                $this->addFlash('success', 'Votre recette n\'existe pas !');
            }
            $manager->remove($manager->getRepository(Recipe::class)->findOneBy(['id' => $id]));
            $manager->flush();

            $this->addFlash('success', 'Votre recette a bien été supprimé avec succès !');

            return $this->redirectToRoute('recipe.index');
        }
}
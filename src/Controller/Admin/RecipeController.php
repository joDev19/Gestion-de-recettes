<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/admin/recettes', name: 'admin.recipe.')]
final class RecipeController extends AbstractController
{
    #[Route('/', name: 'index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_USER');
        // $platPrincipal = $categoryRepository->findOneBy(['slug'=>'plat-de-resistance']);
        // $pate = $repository->findOneBy(['slug'=>'pates-bolognaises']);
        // $pate->setCategory($platPrincipal);
        // $em->flush();
        //dd($platPrincipal);
        //$recipes = $repository->findWithDurationLowerThan(25);  
        $page =  $request->query->getInt('page', 1);
        $limit = 2;
        $recipes = $repository->paginateRecipes($page, $limit);
        $maxPage = ceil($recipes->count() / $limit);
        // dd($recipes->count());
        // dd($repository->findTotalDuration()); 
        return $this->render('admin/recipe/index.html.twig', [
            "recipes" => $recipes,
            "maxPage" => $maxPage,
            'page' => $page
        ]);
    }
    #[Route('/{slug}-{id}', name: 'show', requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {

        $recipe = $repository->find($id);
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipe.show', ['slug' => $recipe->getSlug(), 'id' => $id]);
        }
        return $this->render('recipe/show.html.twig', [
            'slug' => $slug,
            'id' => $id,
            'recipe' => $recipe
        ]);
    }
    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Request $request, Recipe $recipe, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setUpdatedAt(new DateTimeImmutable());

            /**@var UploadedFile $file */
            // $file = $form->get('thumbnailFile')->getData();
            // $filename = $recipe->getId().'.'.$file->getClientOriginalExtension();
            // $file->move($this->getParameter('kernel.project_dir').'/public/recettes/images', $filename);
            // $recipe->setMthumbnail($filename);
            $em->flush();
            $this->addFlash('success', 'La recette à bien ete modifiée');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render('admin/recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form
        ]);
    }
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $recipe->setCreatedAt(new DateTimeImmutable())->setUpdatedAt(new DateTimeImmutable());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La tâche à été créer avec succes');
            return $this->redirectToRoute('admin.recipe.index');
        }
        return $this->render(
            'admin/recipe/create.html.twig',
            [
                'form' => $form,
                'recipe' => $recipe
            ]
        );
    }
    #[Route('/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function remove(Recipe $recipe, EntityManagerInterface $em)
    {
        $em->remove($recipe);
        $em->flush();
        $this->addFlash(
            'success',
            'La recette a bien été supprimé'
        );
        return $this->redirectToRoute('admin.recipe.index');
    }
}

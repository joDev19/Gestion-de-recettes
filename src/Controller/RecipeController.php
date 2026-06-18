<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RecipeController extends AbstractController
{
    #[Route('/recettes', name: 'recipe.index')]
    public function index(Request $request, RecipeRepository $repository): Response
    {
        $recipes = $repository->findWithDurationLowerThan(25);  
        // dd($repository->findTotalDuration()); 
        return $this->render('recipe/index.html.twig',[
            "recipes" => $recipes
        ]);
    }
    #[Route('/recettes/{slug}-{id}', name: 'recipe.show', requirements: ['id' => '\d+', 'slug'=>'[a-z0-9-]+'])]
    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {

        $recipe = $repository->find($id);
        if($recipe->getSlug() !== $slug){
            return $this->redirectToRoute('recipe.show',['slug'=>$recipe->getSlug(), 'id'=>$id]);
        }
        return $this->render('recipe/show.html.twig', [
            'slug' => $slug,
            'id' => $id,
            'recipe' => $recipe
        ]);
    }
}

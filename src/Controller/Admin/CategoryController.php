<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories', name: 'admin.categories.')]
#[IsGranted('ROLE_ADMIN')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/create', name: 'create')]
    public function create(EntityManagerInterface $em, Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'La catégorie a été créer avec succes');
            return $this->redirectToRoute('admin.categories.index');
        }
        return $this->render('admin/category/create.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/edit/{id}', name: 'edit', requirements: ['id'=>Requirement::DIGITS])]
    public function edit(Request $request, Category $category, EntityManagerInterface $em,)
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie modifié avec succes');
            return $this->redirectToRoute('admin.categories.index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }
    #[Route('/{id}', name:'delete', requirements:['id'=>'\d+'], methods:['DELETE'])]
    public function remove(Category $category, EntityManagerInterface $em){
        $em->remove($category);
        $em->flush();
        $this->addFlash(
           'success',
           'La categorie a bien été supprimé'
        );
        return $this->redirectToRoute('admin.categories.index');
    }
}

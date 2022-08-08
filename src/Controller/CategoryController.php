<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/active/categories', name: 'category_active_index')]
    public function active(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findByActive(1);
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/categories/create', name: 'category_create')]
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category();
        $category_form = $this->createForm(CategoryFormType::class, $category);
        $category_form->handleRequest($request);

        if ($category_form->isSubmitted() && $category_form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', 'Category ' . $category->getName() . ' Created Successfully!');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/create.html.twig', [
            'category_form' => $category_form->createView(),
        ]);
    }

    #[Route('/categories/edit/{id}', name: 'category_update')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $category = $doctrine->getManager()->getRepository(Category::class)->find($id);
        if (!$category) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('notice', 'Category ' . $category->getName() . ' Updated Successfully!');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('category/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/categories/delete/{id}', name: 'category_delete')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $category = $doctrine->getManager()->getRepository(Category::class)->find($id);
        if (!$category) {
            throw new NotFoundHttpException();
        }
        $em = $doctrine->getManager();
        $em->remove($category);
        $em->flush();

        $this->addFlash('notice', 'Deleted Category Successfully!!');

        return $this->redirectToRoute('category_index');
    }
}

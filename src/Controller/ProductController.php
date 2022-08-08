<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'products_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/create', name: 'product_create')]
    public function store(Request $request, ManagerRegistry $doctrine): Response
    {
        $product = new Product();
        $product_form = $this->createForm(ProductFormType::class, $product);
        $product_form->handleRequest($request);

        if ($product_form->isSubmitted() && $product_form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash('notice', 'Product ' . $product->getName() . ' Created Successfully!');

            return $this->redirectToRoute('products_index');
        }

        return $this->render('product/create.html.twig', [
            'product_form' => $product_form->createView(),
        ]);
    }

    #[Route('/product/edit/{id}', name: 'product_update')]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $product = $doctrine->getManager()->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newProduct = $form->getData();
            $em = $doctrine->getManager();
            $em->persist($newProduct);
            $em->flush();

            $this->addFlash('notice', 'Product ' . $product->getName() . ' Updated Successfully!');

            return $this->redirectToRoute('products_index');
        }

        return $this->render('product/update.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/product/delete/{id}', name: 'product_delete')]
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        $product = $doctrine->getManager()->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new NotFoundHttpException();
        }
        $em = $doctrine->getManager();
        $em->remove($product);
        $em->flush();

        $this->addFlash('notice', 'Deleted Product Successfully!!');

        return $this->redirectToRoute('products_index');
    }
}

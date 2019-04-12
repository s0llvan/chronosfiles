<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CategoryType;
use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
    * @Route("/categories", name="categories")
    */
    public function index(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $category->setUser($user);

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $category = new Category();
            $form = $this->createForm(CategoryType::class, $category);
        }

        return $this->render('categories.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route("/category/{id}", name="category_edit")
    */
    public function edit(Request $request, Category $category)
    {
        $user = $this->getUser();
        if($category->getUser() == $user)
        {
            $form = $this->createForm(CategoryType::class, $category);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $category = new Category();
                $form = $this->createForm(CategoryType::class, $category, [
                    'action' => $this->generateUrl('categories')
                ]);
            }
        }

        return $this->render('categories.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
    * @Route("/category/{id}/delete", name="category_delete")
    */
    public function delete(Request $request, Category $category)
    {
        $user = $this->getUser();
        if($category->getUser() == $user)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('categories'));
    }
}

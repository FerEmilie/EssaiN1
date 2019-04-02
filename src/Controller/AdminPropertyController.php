<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use App\Form\PropertyType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;

class AdminPropertyController extends AbstractController
{
  /**
   * @var PropertyRepository
   */
   private $repository;

   public function __construct(PropertyRepository $repository, ObjectManager $em)
   {
     $this->repository = $repository;
     $this->em = $em;
   }
    /**
     * @Route("/admin", name="admin.property.index")
     */
    public function index()
    {
        $properties = $this->repository->findAll();
        return $this->render('admin_property/index.html.twig', compact('properties'));
    }
    /**
     * @Route("/admin/create", name="admin.property.new")
     */
    public function new(Request $request)
    {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
          $this->em->persist($property);
          $this->em->flush();
          $this->addFlash('success', 'Création effectuée avec succès');
          return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin_property/new.html.twig', [
        'property' => $property,
        'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/edit/{id}", name="admin.property.edit")
     */
    public function edit(Property $property, Request $request)
    {
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
          $this->em->flush();
          $this->addFlash('success', 'Modification effectuée avec succès');
          return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin_property/edit.html.twig', [
        'property' => $property,
        'form' => $form->createView()]);
    }

    /**
     * @Route("/admin/delete/{id}", name="admin.property.delete", methods="DELETE")
     */
     public function delete(Property $property, Request $request)
     {
        if($this->isCsrfTokenValid('delete', $property->getId(), $request->get('_token') )){
          $this->em->remove($property);
          $this->em->flush();
          $this->addFlash('success', 'Suppression effectuée avec succès');
        }
        return $this->redirectToRoute('admin.property.index');

     }

}

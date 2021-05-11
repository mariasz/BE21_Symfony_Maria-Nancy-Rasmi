<?php
namespace App\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Inventory;

class InventoryController extends AbstractController
{
    /**
     * @Route("/", name="inventory")
     */
    public function index(): Response
    {
        $inventories = $this->getDoctrine()->getRepository('App:Inventory')->findAll();
        return $this->render('inventory/index.html.twig', array('inventories'=>$inventories));
    }

    /**
     * @Route("/create", name="inventory_create")
     */
    public function create(Request $request): Response
    {
        $inventory = new Inventory;
        $form = $this->createFormBuilder($inventory)->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('quantity', IntegerType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('weight', IntegerType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('category', TextType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('notes', TextareaType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('image', TextType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
        ->add('save', SubmitType::class, array('label'=> 'Create Item', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))
        ->getForm();
        $form->handleRequest($request);       
        
        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $quantity = $form['quantity']->getData();
            $weight = $form['weight']->getData();
            $category = $form['category']->getData();
            $notes = $form['notes']->getData();
            $image = $form['image']->getData();
           
            $inventory->setName($name);
            $inventory->setQuantity($quantity);
            $inventory->setWeight($weight);
            $inventory->setCategory($category);
            $inventory->setNotes($notes);
            $inventory->setImage($image);
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventory);
            $em->flush();
            $this->addFlash(
                    'notice',
                    'Item Added'
                    );
            return $this->redirectToRoute('inventory');
        }
        return $this->render('inventory/create.html.twig', array('form' => $form->createView()));
    }

    
    /**
    * @Route("/edit/{id}", name="inventory_edit")
    */
    public function edit(Request $request, $id): Response
    {
        $inventory = $this->getDoctrine()->getRepository('App:Inventory')->find($id);
        
        $inventory->setName($inventory->getName());
        $inventory->setQuantity($inventory->getQuantity());
        $inventory->setWeight($inventory->getWeight());
        $inventory->setCategory($inventory->getCategory());
        $inventory->setNotes($inventory->getNotes());
        $inventory->setImage($inventory->getImage());

        $form = $this->createFormBuilder($inventory)->add('name', TextType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-botton:15px')))
            ->add('quantity', IntegerType::class, array('attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
            ->add('weight', IntegerType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
            ->add('category', TextType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
            ->add('notes', TextareaType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
            ->add('image', TextType::class, array("required"=>false,'attr' => array('class'=> 'form-control', 'style'=>'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('label'=> 'Create Item', 'attr' => array('class'=> 'btn-primary', 'style'=>'margin-bottom:15px')))
            ->getForm();
        $form->handleRequest($request);
 
        if($form->isSubmitted() && $form->isValid()){
            $name = $form['name']->getData();
            $quantity = $form['quantity']->getData();
            $weight = $form['weight']->getData();
            $category = $form['category']->getData();
            $notes = $form['notes']->getData();
            $image = $form['image']->getData();
            
            $em = $this->getDoctrine()->getManager();
            $inventory = $em->getRepository('App:Inventory')->find($id);
            $inventory->setName($name);
            $inventory->setQuantity($quantity);
            $inventory->setWeight($weight);
            $inventory->setCategory($category);
            $inventory->setNotes($notes);
            $inventory->setImage($image);
            
            $em->flush();
            $this->addFlash(
                    'notice',
                    'Item Updated'
                    );
            return $this->redirectToRoute('inventory');
        }
        return $this->render('inventory/edit.html.twig', array('inventory' => $inventory, 'form' => $form->createView()));  
    }    

    /**
    * @Route("/details/{id}", name="inventory_details")
    */
    public function details($id): Response
    {
        $inventory = $this->getDoctrine()->getRepository('App:Inventory')->find($id);
        return $this->render('inventory/details.html.twig', array('inventory' => $inventory));
    }

    /**
     * @Route("/delete/{id}", name="inventory_delete")
     */
    public function delete($id){
        $em = $this->getDoctrine()->getManager();
        $inventory = $em->getRepository('App:Inventory')->find($id);
        $em->remove($inventory);
        $em->flush();
        $this->addFlash(
            'notice',
            'Item Removed'
        );
        return $this->redirectToRoute('inventory');
    }
}
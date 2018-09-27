<?php

namespace App\Controller;

use App\Entity\Users;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class UsersController extends Controller
{

    /**
     * @route ("/", name="users_show")
     * Redirect to the list of all the users
     */

     public function show_users()
    {       
                return $this->redirectToRoute('users_all');
    } 
   
    /**
     * @Route("/users", name="users_all")
     * Listing all users
     * @var search, users, paginator
     * @param Request, UserInterface
     * @return all users, logged user detail, search form
     */
    
    public function index(Request $request, UserInterface $userLogged)
    {

            $search = $request->query->get('search'); 

            //get all users
            $users = $this->getDoctrine()
                    ->getRepository(Users::class)
                    ->showUsersByAttrs($search);


             // using knp paginator for pagination records
            $paginator = $this->get('knp_paginator');

        $result = $paginator -> paginate(
            $users,
            $request->query->get('page',1),
            5
        );

        // search user form
        $formSearch = $this->createFormBuilder(null)           
            ->add('search', TextType::class,
                    array('required' => false,
                    'attr' => array('value' => $search)
            ))
            ->add('search_bttn', SubmitType::class, array('label' => 'Search', 'attr' => array('class' => 'hidden')))
            ->getForm();


        $formSearch->handleRequest($request);


        // if search user form submits
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            // get serch text
                echo $search_txt = $_REQUEST['form']['search'];
                
            // return to show all users route
                return $this->redirectToRoute('users_all', array('search' => $search_txt));
        }
     


            return $this->render('users/index.html.twig', [            
                'users' => $result,
                'logged_user' => $userLogged,                    
                'search_form' => $formSearch->createView()
            ]);
    }






    

    /**
     * @Route("/new", name="user_new")
     * @var Request, UserPasswordEncoderInterface
     */

    //Adding a new user
public function newUser(Request $request, UserPasswordEncoderInterface $encoder)
    {         
            $user = new Users();           

            $form = $this->createFormBuilder($user)           
                ->add('username', TextType::class)
                ->add('email', TextType::class)
                ->add('mobile', TextType::class,array(
                     'required' => false
                    ))
                ->add('password', TextType::class)
                ->add('exp', ChoiceType::class, array(
                          'choices'  => array(
                                'Less than 1 Yr' => 0,
                                '1 Yr' => 1,
                                'Greater than 1 Yr' => 2,
                      )))
                ->add('about', TextareaType::class,array(
                     'required' => false
                	))
                ->add('save', SubmitType::class, array('label' => 'Add user'))
                ->getForm();


            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                   
                $userData = $form->getData();

          

            $userData->setPassword(  $encoder->encodePassword( $user, $userData->getPassword() )  );
           
            
             $entityManager = $this->getDoctrine()->getManager();
             $entityManager->persist($userData);
             $entityManager->flush();

            $this->addFlash(
                        'notice',
                        'User Added with id #'.$userData->getId()
                    );

            return $this->redirectToRoute('users_all');
        }


            return $this->render('users/new.html.twig', array(
                'formNew' => $form->createView(),
            ));
    }


    /**
     * @Route("/user/{id}", name="user_view")
     * view a user details by id
     * @return user array
     */
    
    public function user($id)
    {
        	$user = $this->getDoctrine()
                ->getRepository(Users::class)
                ->find($id);



            return $this->render('users/view.html.twig', [
                'user' => $user,
            ]);
    }


    /**
     * @Route("/delete/{id}", name="user_delete", methods={"GET"})
     * delete a user by id
     */
    public function delete($id)
    {
        	$user = $this->getDoctrine()
                ->getRepository(Users::class)
                ->find($id);

            if($user) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager -> remove($user);
                $entityManager -> flush();

                $this->addFlash(
                    'notice',
                    'User deleted!'
                );
            }


            return $this->redirectToRoute('users_all');
    }

    /**
     * @Route("/update/{id}", name="user_update")
     * update a user by id
     */
     
    public function update(Request $request,$id)
    {
            $user = $this->getDoctrine()
                ->getRepository(Users::class)
                ->find($id);

            $form = $this->createFormBuilder($user)           
                ->add('username', TextType::class)
                ->add('email', TextType::class)            
                ->add('exp', ChoiceType::class, array(
                'choices'  => array(
                'Less than 1 Yr' => 0,
                '1 Yr' => 1,
                'Greater than 1 Yr' => 2,
                )))
                ->add('about', TextareaType::class)
                ->add('save', SubmitType::class, array('label' => 'Update user'))
                ->getForm();



            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'User updated!'
                );

                return $this->redirectToRoute('users_all');
            }


            return $this->render('users/update.html.twig', array(
                'form' => $form->createView(), 'user' => $user
            ));


            }
   

}

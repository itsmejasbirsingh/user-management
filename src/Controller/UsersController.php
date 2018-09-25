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

//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class UsersController extends Controller
{

/**
 * @route ("/api/frameworks", name="get_frameworks_api")
 */

public function getBudgets(Request $request)
{

$data = new JsonResponse([
                "frameworks" => [
                [
                       "id" => 1,
                       "name" => 'Cakephp'                      
                ],
                [
                       "id" => 2,
                       "name" => 'Symfony'                
                ],
                [
                       "id" => 3,
                       "name" => 'Code Ignitor'
                ]
            ]
    ]);


 return $this->render('api/index.html.twig', [                               
            'json' => $data 
        ]);

}


/**
 * @route ("/login", name="login")
 */

/* public function login()
{

        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_USER')) 
        {
            return $this->redirectToRoute('users_all');
        }


} */

/**
 * @route ("/", name="users_show")
 */

 public function show_users()
{       
            return $this->redirectToRoute('users_all');
} 
   
    /**
     * @Route("/users", name="users_all")
     */
    public function index(Request $request,SessionInterface $session, UserInterface $userLogged)
    {




$search = $request->query->get('search');


$users = $this->getDoctrine()
        ->getRepository(Users::class)
        ->showUsersByAttrs($search);
        


$paginator = $this->get('knp_paginator');
$result = $paginator -> paginate(
      $users,
      $request->query->get('page',1),
      5
    );


        $formSearch = $this->createFormBuilder(null)           
            
            ->add('search', TextType::class,
              array('required' => false,
                    'attr' => array('value' => $search)
                    ))
            ->add('search_bttn', SubmitType::class, array('label' => 'Search', 'attr' => array('class' => 'hidden')))
            ->getForm();


$formSearch->handleRequest($request);

if ($formSearch->isSubmitted() && $formSearch->isValid()) {
   
 $search_txt = $_REQUEST['form']['search'];

  
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
     */
public function newUser(Request $request,SessionInterface $session, UserPasswordEncoderInterface $encoder)
    {
   
        // creates a task and gives it some dummy data for this example
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




/**
     * @Route("/{_locale}/locale", name="locale")
     */
public function getloc(Request $request)
{
     $locale = $request->getLocale();
     return new Response($locale);
}

/**
     * @Route("/exp-greater-than/{exp}", name="get_experience_greater_than")
     */
public function expGreaterThan($exp)
{
     $exps = $this->getDoctrine()
    ->getRepository(Users::class)
    ->findAllGreaterThanExp($exp);


    echo "<pre>";
print_r($exps);
die;

}



   /**
     * @Route({
     *     "nl": "{locale}/over-ons",
     *     "en": "/about-us"
     * }, name="about_us")
     */
    public function about($locale)
    {
        echo "about us";
        die;
    }



}

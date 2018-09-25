<?php

namespace App\Controller;
use App\Entity\Users;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ApiUsersController extends Controller
{   


	/**
     * @Route("/api/user/delete/{id}", name="api_user_delete", methods={"POST"})
     */
    public function delete($id)
    {
       $data = array();
$data['success'] = false;
$data['message'] = 'Something went wrong!!!';


  $user = $this->getDoctrine()->getRepository(Users::class)->find($id);

  if($user)
  {
     $data['success'] = true;
     $data['message'] = $user->getUsername().' Deleted';
     $entityManager = $this->getDoctrine()->getManager();
$entityManager -> remove($user);
$entityManager -> flush();
    
  }
  else
  {
  	$data['message'] = 'User not found!';
  }

echo json_encode($data);
die;

    }

	/**
     * @Route("/api/user/view/{id}", name="api_user_view")
     */
    public function view($id)
    {
       $data = array();
$data['success'] = false;
$data['message'] = 'Something went wrong!!!';


  $user = $this->getDoctrine()->getRepository(Users::class)->find($id);
  if($user)
  {
     $data['success'] = true;
     $data['message'] = 'User found';
     $data['data']['username'] = $user->getUsername();
     $data['data']['email'] = $user->getEmail();
  }
  else
  {
  	$data['message'] = 'User not found!';
  }

echo json_encode($data);
die;

    }

    /**
     * @Route("/api/user/update/{id}", name="api_user_update", methods={"POST"})
     */
    public function update(Request $request, UserPasswordEncoderInterface $encoder,$id)
    {
    	
$data = array();
$data['success'] = false;
$data['message'] = 'Something went wrong!!!';


  $user = $this->getDoctrine()->getRepository(Users::class)->find($id);
  if($user)
  {
  	$username = $request->request->get('username');
  	$password = $request->request->get('password');
  $user->setUsername($username);
  $user->setPassword(  $encoder->encodePassword( $user, $password )  );

         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->flush();


         $data['success'] = true;
		 $data['message'] = 'User Successfully updated!';
  }
  else
  {
  	$data['message'] = 'User not found!';
  }


         echo json_encode($data);
         die;

    }

     /**
     * @Route("/api/users/", name="api_users")
     */
    public function index(Request $request)
    {

$search = $request->query->get('search');

$users = $this->getDoctrine()
        ->getRepository(Users::class)
        ->showUsersByAttrs($search);

	/**
     * @var $paginator \Knp\Component\Pager\Paginator
     */

$paginator = $this->get('knp_paginator');
$result = $paginator -> paginate(
      $users,
      $request->query->get('page',1),
      5
    );

//echo json_encode($result);
print_r($result);
die;
    }

   /**
     * @Route("/api/user/add", name="api_user_add", methods={"POST"})
     */
    public function add(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $data = array();

        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $password = $request->request->get('passsword');
        $about = $request->request->get('about');
        $exp = $request->request->get('exp');
        $mobile = $request->request->get('mobile');

$data['success'] = false;


if( isset($username) && trim($username) )
{

	if(isset($email) && trim($email) )
	{
         $userEmail = $this->getDoctrine()
        ->getRepository(Users::class)
        ->findByEmail($email);

        if(!count($userEmail))
        {
         
           $user = new Users();
       
       $user->setUsername($username);
       $user->setEmail($email);
       $user->setAbout($about);
       $user->setExp($exp);
       $user->setMobile($mobile);
        $user->setPassword(  $encoder->encodePassword( $user, $password )  );
       
        
         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->persist($user);
         $entityManager->flush();


$data['success'] = true;
$data['message'] = "User successfully inserted with id #".$user->getId();

        }
        else
        {
        	
		    $data['message'] = "Email Already exist";
        }
	}
	else
	{
		
		$data['message'] = "Email can not be empty";
	}

}
else
{
	
	$data['message'] = "Username can not be empty";
}



         

echo json_encode($data);

die;



    


    }

}

<?php
namespace App\Controller;
use App\Entity\Users;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;
use FOS\RestBundle\Controller\FOSRestController;

class ApiUsersController extends FOSRestController
{   
		private function findModel($id)
		{
			if (($user = $this->getDoctrine()->getRepository(Users::class)->find($id)) != NULL) {
				return $user;
			} else {
				throw new \Exception(Users::USER_NOT_FOUND, 1); 
			}
		}

		/**
		* @Route("/api/user/delete/{id}", name="api_user_delete", methods={"POST"})
		* Delete a user
		*/
		public function delete($id)
		{
			$user = $this->findModel($id);
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager -> remove($user);
			$entityManager -> flush();
			return [Users::USER_DELETED]; 
		}

		/**
		* @Route("/api/user/view/{id}", name="api_user_view")
		* view a user by id
		*/
		public function find($id)
		{
			return $this->findModel($id);
		}

		/**
		* @Route("/api/user/update/{id}", name="api_user_update", methods={"POST"})
		* Update a user by id
		*/
		public function update(Request $request, UserPasswordEncoderInterface $encoder, $id)
		{
			$user = $this->findModel($id);

			$user->setUsername($request->request->get('username'));
			$user->setPassword(  $encoder->encodePassword($user, $request->request->get('password')));

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->flush();

			return [Users::USER_UPDATED]; 
		}

		/**
		* @Route("/api/users/", name="api_users")
		* show all users with knp pagination
		* @var search_text
		*/
		public function index(Request $request)
		{
			$search = $request->query->get('search');

			$users = $this->getDoctrine()
				->getRepository(Users::class)
				->showUsersByAttrs($search);

			$paginator = $this->get('knp_paginator');

			return $paginator -> paginate(
				$users,
				$request->query->get('page', 1),
				Users::MAX_PAGE_LIMIT 
			);

		}

		/**
		* @Route("/api/user/add", name="api_user_add", methods={"POST"})
		* add a user
		*/
		public function add(Request $request, UserPasswordEncoderInterface $encoder)
		{     	
			$return = [];
			$inputs = $request->request->all();

			$repository = $this->getDoctrine()->getRepository(Users::class);

			if ($repository->validate($inputs)) {
				$user = new Users();

				$user->setUsername($inputs['username']);
				$user->setEmail($inputs['email']);
				$user->setExp($inputs['exp']);
				$user->setAbout($inputs['about']);
				$user->setMobile($inputs['mobile']);

				$password = $inputs['password'];
				$user->setPassword(  $encoder->encodePassword( $user, $password )  );
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist($user);
				$entityManager->flush();

				return [Users::USER_ADDED];
			} else {
				$return = $repository->errors;
			}

			return $return;
		}
}
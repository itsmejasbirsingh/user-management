<?php
// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{


    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $utils,Request $request)
    {
// get the login error if there is one
    $error = $utils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $utils->getLastUsername();

    return $this->render('security/login.html.twig', array(
        'last_username' => $lastUsername,
        'error'         => $error,
    ));

    }


/**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }

}
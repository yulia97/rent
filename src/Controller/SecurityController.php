<?php

// src/Controller/SecurityController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;


use App\Form\UserType;
use App\Entity\User;
use App\Entity\UserInfo;


use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



class SecurityController extends Controller
{
     /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
	// get the login error if there is one
	$error = $authenticationUtils->getLastAuthenticationError();
	
	// last username entered by the user
	$lastUsername = $authenticationUtils->getLastUsername();
	
	return $this->render('login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
	));
    }



    /**
     * @Route("/register", name="registration")
     * @Method({"GET", "POST"})
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        



	$user = new User();
	$userInfo = new UserInfo();
	
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
	    $user->setUserInfo($userInfo);
            // 4) save the User!
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('index');
        }

        return $this->render(
            'register.html.twig',
            array('form' => $form->createView())
        );
    }
}

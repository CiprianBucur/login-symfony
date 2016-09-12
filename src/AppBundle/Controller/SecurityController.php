<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request) {
		$authenticationUtils = $this->get('security.authentication_utils');
		$error = $authenticationUtils->getLastAuthenticationError();
		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render('AppBundle:Security:login.html.twig',
			array(
				'last_username' => $lastUsername,
				'error'         => $error,
		));
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request) {
		return new Response('<html><body>Registration page</body></html>');
    }

}

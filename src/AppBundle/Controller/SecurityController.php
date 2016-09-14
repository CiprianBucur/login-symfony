<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends Controller {

    /**
     * @Route("/{_locale}/login", name="login")
     */
    public function loginAction(Request $request, $_locale="en") {

        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->render('default/index.html.twig', array ('limba' => $request->getLocale()));
        }
        else {
            $authenticationUtils = $this->get('security.authentication_utils');
            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render('AppBundle:Security:login.html.twig',
                array(
                    'last_username' => $lastUsername,
                    'error' => $error,
                    'limba' => $request->getLocale()
                ));
        }
    }

	/**
	 * @Route("/{_locale}/register", name="register")
	 */
	public function registerAction(Request $request, $_locale="en") {
		return $this->render('default/register.html.twig', array ('limba' => $request->getLocale()));
	}
}

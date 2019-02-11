<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function loginAction(Request $request) {
		$authenticationUtils = $this->get('security.authentication_utils');

		// get the login error if there is one
		$error = $authenticationUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authenticationUtils->getLastUsername();

		// Csrf token generation
		$csrfToken = $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue();

		return $this->render('@SfsAdmin/Security/login.html.twig', array(
			'last_username' => $lastUsername,
			'error'         => $error,
			'csrf_token' 	=> $csrfToken
		));
	}


	/**
	 * Symfony is supposed to intercept the logout call, only a misconfiguration would lead to go into the action
	 *
	 * @throws RuntimeException
	 */
	public function logoutAction() {
		throw new \RuntimeException('Configure the logout entry in your firewall.');
	}
}

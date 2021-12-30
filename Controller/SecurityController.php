<?php

/**
 * SfsAdminBundle - Symfony2 project
 *
 * @author Ramine AGOUNE <ramine.agoune@solidlynx.com>
 */

namespace Sfs\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


class SecurityController extends AbstractController
{
	private $authenticationUtils;
	private $tokenManager;

	public function __construct(AuthenticationUtils $authenticationUtils, CsrfTokenManagerInterface $tokenManager)
	{
		$this->authenticationUtils = $authenticationUtils;
		$this->tokenManager = $tokenManager;
	}

	/**
	 * @return Response
	 */
	public function loginAction()
	{
		$error = $this->authenticationUtils->getLastAuthenticationError();
		$lastUsername = $this->authenticationUtils->getLastUsername();

		$csrfToken = $this->tokenManager
			? $this->tokenManager->getToken('authenticate')->getValue()
			: null;

		return $this->render('@SfsAdmin/Security/login.html.twig', array(
			'last_username' => $lastUsername,
			'error'         => $error,
			'csrf_token' 	=> $csrfToken
		));
	}

	public function checkAction()
	{
		throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
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

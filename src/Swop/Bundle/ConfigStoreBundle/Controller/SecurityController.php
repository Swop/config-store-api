<?php
/*
 * This file licensed under the MIT license.
 *
 * (c) Sylvain Mauduit <swop@swop.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Swop\Bundle\ConfigStoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($username = $session->get(SecurityContextInterface::LAST_USERNAME)) {
            $session->remove(SecurityContextInterface::LAST_USERNAME);
        }

        if ($error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        }

        $loginForm = $this->createForm('login');
        $loginForm
            ->add('login', 'submit');

        return $this->render('SwopConfigStoreBundle:Security:login.html.twig', [
            'last_username' => $username,
            'error'         => $error,
            'loginForm'     => $loginForm->createView()
        ]);
    }
}

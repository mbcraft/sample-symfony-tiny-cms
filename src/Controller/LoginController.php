<?php

namespace App\Controller;

use App\FormData\Login;
use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/admin", name="app_login")
     */
    public function index(): Response
    {

        $login_data = new Login();

        $login_form = $this->createForm(LoginType::class,$login_data);

        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController', 'form' => $login_form->createView()
        ]);
    }
}

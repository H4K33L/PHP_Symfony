<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    #[Route('/Dashboard')]
    public function display()
    {
        return $this->render('dashboard.html.twig');
    }
}
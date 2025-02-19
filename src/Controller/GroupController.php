<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AbstractController
{
    #[Route('/groupManager', name: 'groupManager')]
    public function display_group()
    {
        return $this->render('groupe.html.twig');
    }
}
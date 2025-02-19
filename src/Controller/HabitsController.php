<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HabitsController extends AbstractController
{
    #[Route('/habitsManager', name: 'habitsManager')]
    public function display_habits_manager()
    {
        return $this->render('habits.html.twig');
    }
}
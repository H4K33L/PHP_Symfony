<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PointsController extends AbstractController
{
    #[Route('/points', name: 'points')]
    public function display_points()
    {
        return $this->render('point.html.twig');
    }
}
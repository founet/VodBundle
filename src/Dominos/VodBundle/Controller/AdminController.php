<?php

namespace Dominos\VodBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DominosVodBundle:Admin:index.html.twig', array('name' => $name));
    }
}

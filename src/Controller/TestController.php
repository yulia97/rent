<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class TestController extends Controller
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

   /**
     * @Route("/", name="index")
     */
    public function fdfs()
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'INDEX',
        ]);
    }
}

<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendController extends Controller
{
    public function indexAction(Request $request): Response
    {
        $template = $request->attributes->get('template', 'Front/index.html.twig');
        return $this->render($template);
    }
}

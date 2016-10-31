<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $locale = $request->getLocale();
        echo "<pre><h2>" . $locale . "</h2></pre>";
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/about", defaults={"_locale"="pl"})
     * @Route("/{_locale}/about", requirements={"_locale" = "pl|en|de"})
     */
    public function aboutAction(Request $request)
    {
        // Route("/{_locale}/about", defaults={"_locale" = "pl"})
        $locale = $request->getLocale();
        echo "<pre><h2>" . $locale . "</h2></pre>";
        return $this->render('default/about.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/services")
     */
    public function servicesAction()
    {
        return $this->render('default/services.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }

    /**
     * @Route("/contact")
     */
    public function contactAction()
    {
        return $this->render('default/contact.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}

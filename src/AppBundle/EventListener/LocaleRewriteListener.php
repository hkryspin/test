<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
//use Symfony\Component\HttpFoundation\Session\Session;
//use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;

class LocaleRewriteListener implements EventSubscriberInterface
{
    private $router;
    private $routeCollection;
    //private $urlMatcher;
    private $defaultLocale;
    private $supportedLocales;
    private $localeRouteParam;

    public function __construct(RouterInterface $router, $defaultLocale = 'pl', array $supportedLocales, $localeRouteParam = '_locale')
    {
        $this->router = $router;
        $this->routeCollection = $router->getRouteCollection();
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
        $this->localeRouteParam = $localeRouteParam;
        $context = new RequestContext("/");
        $this->matcher = new UrlMatcher($this->routeCollection, $context);
    }

    public function isLocaleSupported($locale)
    {
        return array_key_exists($locale, $this->supportedLocales);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        //GOAL:
        // Redirect all incoming requests to their /locale/route equivalent when exists.
        // Do nothing if it already has /locale/ in the route to prevent redirect loops
        // Do nothing if the route requested has no locale param

        $request = $event->getRequest();
        $baseUrl = $request->getBaseUrl();
        $path = $request->getPathInfo();

        //Get the locale from the users browser.
        $locale = $request->getPreferredLanguage();

        if ($this->isLocaleSupported($locale)) {
            $locale = $this->supportedLocales[$locale];
        } else if ($locale == ""){
            $locale = $request->getDefaultLocale();
        }

        $pathLocale = "/".$locale.$path;

        //We have to catch the ResourceNotFoundException
        try {
            //Try to match the path with the local prefix
            $this->matcher->match($pathLocale);
            $event->setResponse(new RedirectResponse($baseUrl.$pathLocale));
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {

        } catch (\Symfony\Component\Routing\Exception\MethodNotAllowedException $e) {

        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            // must be registered before the default Locale listener
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
}
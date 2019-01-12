<?php
/**
 * Created by PhpStorm.
 * User: oussaka
 * Date: 11/01/19
 * Time: 00:09
 */

namespace AppBundle\Event\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Templating\EngineInterface;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    private $maintenance;
    private $env;
    private $templating;

    public function __construct($maintenance, $env, EngineInterface $templating)
    {
        $this->maintenance = $maintenance;
        $this->env = $env;
        $this->templating = $templating;
    }

    public static function getSubscribedEvents() : array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        // This will detect if we are in dev environment (app_dev.php)
        $debug = in_array($this->env, ['dev']);

        // If maintenance is active and in prod environment
        if ($this->maintenance && !$debug) {
            // We load our maintenance template
            $template = $this->templating->render('::maintenance.html.twig');

            // We send our response with a 503 response code (service unavailable)
            $event->setResponse(new Response($template, 503));
            $event->stopPropagation();
        }
    }
}
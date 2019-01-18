<?php

namespace Kiboko\Bundle\DAMBundle\Controller;

use Oro\Bundle\DashboardBundle\Model\Manager;
use Oro\Bundle\DashboardBundle\Model\WidgetConfigs;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/dashboard", service="kiboko_dam.controller.dashboard")
 */
class DashboardController extends Controller
{
    /**
     * @Route(
     *      "/launchpad",
     *      name="kiboko_dashboard_quick_launchpad"
     * )
     */
    public function quickLaunchpadAction()
    {
        return $this->render(
            'KibokoDAMBundle:Dashboard:quickLaunchpad.html.twig',
            [
                'dashboards' => $this->getDashboardManager()->findAllowedDashboards(),
            ]
        );
    }

    /**
     * @Route(
     *      "/itemized_widget/{widget}/{bundle}/{name}",
     *      name="kiboko_dashboard_itemized_widget",
     *      requirements={"widget"="[\w-]+", "bundle"="\w+", "name"="[\w-]+"}
     * )
     *
     * @param string $widget
     * @param string $bundle
     * @param string $name
     *
     * @return Response
     */
    public function itemizedWidgetAction($widget, $bundle, $name)
    {
        /** @var WidgetConfigs $manager */
        $manager = $this->get('oro_dashboard.widget_configs');

        $params = array_merge(
            [
                'items' => $manager->getWidgetItems($widget),
            ],
            $manager->getWidgetAttributesForTwig($widget)
        );

        return $this->render(
            sprintf('%s:Dashboard:%s.html.twig', $bundle, $name),
            $params
        );
    }

    /**
     * @return Manager
     */
    protected function getDashboardManager()
    {
        return $this->get('oro_dashboard.manager');
    }
}

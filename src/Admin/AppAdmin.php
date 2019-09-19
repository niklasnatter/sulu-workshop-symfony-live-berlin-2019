<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Routing\RouteCollection;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class AppAdmin extends Admin
{
    const EVENT_LIST_ROUTE = 'app.events_list';

    const EVENT_ADD_FORM_ROUTE = 'app.event_add_form';

    const EVENT_EDIT_FORM_ROUTE = 'app.event_edit_form';

    /**
     * @var RouteBuilderFactoryInterface
     */
    private $routeBuilderFactory;

    /**
     * @var WebspaceManagerInterface
     */
    private $webspaceManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        RouteBuilderFactoryInterface $routeBuilderFactory,
        WebspaceManagerInterface $webspaceManager,
        TranslatorInterface $translator
    ) {
        $this->routeBuilderFactory = $routeBuilderFactory;
        $this->webspaceManager = $webspaceManager;
        $this->translator = $translator;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        $module = new NavigationItem('app.events');
        $module->setPosition(40);
        $module->setIcon('fa-calendar');

        $events = new NavigationItem('app.events');
        $events->setPosition(10);
        $events->setMainRoute(static::EVENT_LIST_ROUTE);

        $module->addChild($events);

        $navigationItemCollection->add($module);
    }

    public function configureRoutes(RouteCollection $routeCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();

        $listToolbarActions = ['sulu_admin.add', 'sulu_admin.delete'];
        $listRoute = $this->routeBuilderFactory->createListRouteBuilder(self::EVENT_LIST_ROUTE, '/events/:locale')
            ->setResourceKey('events')
            ->setListKey('events')
            ->setTitle('app.events')
            ->addListAdapters(['table'])
            ->addLocales($locales)
            ->setDefaultLocale($locales[0])
            ->setAddRoute(static::EVENT_ADD_FORM_ROUTE)
            ->setEditRoute(static::EVENT_EDIT_FORM_ROUTE)
            ->addToolbarActions($listToolbarActions);
        $routeCollection->add($listRoute);

        $addFormRoute = $this->routeBuilderFactory->createResourceTabRouteBuilder(self::EVENT_ADD_FORM_ROUTE, '/events/:locale/add')
            ->setResourceKey('events')
            ->setBackRoute(static::EVENT_LIST_ROUTE)
            ->addLocales($locales);
        $routeCollection->add($addFormRoute);

        $addDetailsFormRoute = $this->routeBuilderFactory->createFormRouteBuilder(self::EVENT_ADD_FORM_ROUTE . '.details', '/details')
            ->setResourceKey('events')
            ->setFormKey('event_details')
            ->setTabTitle('sulu_admin.details')
            ->setEditRoute(static::EVENT_EDIT_FORM_ROUTE)
            ->addToolbarActions(['sulu_admin.save'])
            ->setParent(static::EVENT_ADD_FORM_ROUTE);
        $routeCollection->add($addDetailsFormRoute);

        $editFormRoute = $this->routeBuilderFactory->createResourceTabRouteBuilder(static::EVENT_EDIT_FORM_ROUTE, '/events/:locale/:id')
            ->setResourceKey('events')
            ->setBackRoute(static::EVENT_LIST_ROUTE)
            ->setTitleProperty('title')
            ->addLocales($locales);
        $routeCollection->add($editFormRoute);

        /** @var string[] $formToolbarActions */
        $formToolbarActions = [
            'sulu_admin.save',
            'sulu_admin.delete',
            'sulu_admin.toggler' => [
                'label' => $this->translator->trans('app.enable_event', [], 'admin'),
                'property' => 'enabled',
                'activate' => 'enable',
                'deactivate' => 'disable',
            ],
        ];
        $editDetailsFormRoute = $this->routeBuilderFactory->createFormRouteBuilder(static::EVENT_EDIT_FORM_ROUTE . '.details', '/details')
            ->setResourceKey('events')
            ->setFormKey('event_details')
            ->setTabTitle('sulu_admin.details')
            ->addToolbarActions($formToolbarActions)
            ->setParent(static::EVENT_EDIT_FORM_ROUTE);
        $routeCollection->add($editDetailsFormRoute);
    }
}

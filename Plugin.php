<?php

namespace Kanboard\Plugin\Group_owners;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;
use Kanboard\Plugin\Group_owners\Model\GroupOwnerModel;

class Plugin extends Base {
    public function initialize() {

        // Add new entry on dropdown only if user is APP_MANAGER or APP_ADMIN
        $this->template->hook->attach('template:header:dropdown', 'Group_owners:header/dropdown');

        // Override default groups dropdown for platform administrator
        $this->template->setTemplateOverride('group/dropdown', 'Group_owners:group/admin_dropdown');

        $this->template->hook->attach('template:config:sidebar', 'Group_owners:config/sidebar');

        // Declare new models
        $this->container['groupOwnerModel'] = $this->container->factory(function ($c) {
            return new GroupOwnerModel($c);
        });

        // Only APP_MANAGER can create/manage their own groups
        $this->applicationAccessMap->add('GroupOwnersListController', '*', Role::APP_MANAGER);
        $this->applicationAccessMap->add('GroupOwnersCreationController', '*', Role::APP_MANAGER);
        $this->applicationAccessMap->add('GroupOwnersModificationController', '*', Role::APP_MANAGER);
        $this->applicationAccessMap->add('GroupOwnersConfigController', '*', Role::APP_ADMIN);
    }

    // Translation
    public function onStartup() {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getClasses()
    {
        return [
            'Plugin\Group_owners\Model' => [
                'GroupOwnerModel',
            ],
        ];
    }

    public function getPluginName() {
        return 'Group_owners';
    }

    public function getPluginDescription() {
        return t('Allows users to create and manage their groups by themselves');
    }

    public function getPluginAuthor() {
        return 'Cyboulette';
    }

    public function getPluginVersion() {
        return '1.0.1';
    }

    public function getPluginHomepage() {
        return 'https://github.com/Cyboulette/Group_owners';
    }
}
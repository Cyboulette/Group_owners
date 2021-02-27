<li <?= $this->app->checkMenuSelection('GroupOwnersConfigController', 'show', 'Group_owners') ?>>
    <?= $this->url->link(t('GroupOwners plugin settings'), 'GroupOwnersConfigController', 'show', array('plugin' => 'Group_owners')) ?>
</li>
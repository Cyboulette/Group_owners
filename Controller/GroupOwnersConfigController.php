<?php

namespace Kanboard\Plugin\Group_owners\Controller;

use Kanboard\Controller\BaseController;

class GroupOwnersConfigController extends BaseController {

    public function show() {
        $checked = $this->configModel->get('display_group_owners_icon', "0") === "1";

        $this->response->html($this->helper->layout->config('Group_owners:config/show', array(
            'title' => t('GroupOwners plugin settings'),
            'checked' => $checked
        )));
    }

    public function save() {
        $values =  $this->request->getValues();

        if (!isset($values['display_group_owners_icon'])) {
            $values['display_group_owners_icon'] = "0";
        }

        if ($this->configModel->save($values)) {
            $this->flash->success(t('Settings saved successfully.'));
        } else {
            $this->flash->failure(t('Unable to save your settings.'));
        }

        $this->response->redirect($this->helper->url->to('GroupOwnersConfigController', 'show', array('plugin' => 'Group_owners')));
    }
}
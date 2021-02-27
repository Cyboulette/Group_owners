<?php

namespace Kanboard\Plugin\Group_owners\Controller;

use Kanboard\Controller\BaseController;

class GroupOwnersModificationController extends BaseController {

    public function show(array $values = array(), array $errors = array()) {
        if (empty($values)) {
            $values = $this->groupModel->getById($this->request->getIntegerParam('group_id'));
        }

        $this->response->html($this->template->render('Group_owners:group_modification/show', array(
            'errors' => $errors,
            'values' => $values,
        )));
    }

    public function save() {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->groupValidator->validateModification($values);
        $group = $this->groupModel->getById($values['id']);

        // Allows to validate the current name
        if($group
            && strcasecmp($group['name'], $values['name']) === 0
            && sizeof($errors) === 1
            && isset($errors['name']) && sizeof($errors['name']) === 1
            && $errors['name'][0] === t('The name must be unique')) {
            $valid = true;
            $errors = array();
        }

        $isOwner = $this->groupOwnerModel->isOwner($group['id'], $this->userSession->getId());
        if (!$isOwner) {
            $valid = false;
            $errors['name'][] = t('Unable to update your group.').' '.t('You are not owner of this group.');
        }

        if ($valid) {
            if ($this->groupModel->update($values) !== false) {
                $this->flash->success(t('Group updated successfully.'));
                return $this->response->redirect($this->helper->url->to('GroupOwnersListController', 'index', array('plugin' => 'Group_owners')), true);
            } else {
                $this->flash->failure(t('Unable to update your group.'));
            }
        }

        return $this->show($values, $errors);
    }

}
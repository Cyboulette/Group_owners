<?php

namespace Kanboard\Plugin\Group_owners\Controller;

use Kanboard\Controller\BaseController;

class GroupOwnersCreationController extends BaseController {

    /**
     * Display a form to create a new group
     *
     * @access public
     * @param array $values
     * @param array $errors
     */
    public function show(array $values = array(), array $errors = array())
    {
        $this->response->html($this->template->render('Group_owners:group_creation/show', array(
            'errors' => $errors,
            'values' => $values,
        )));
    }

    /**
     * Validate and save a new group
     *
     * @access public
     */
    public function save() {
        $values = $this->request->getValues();
        list($valid, $errors) = $this->groupValidator->validateCreation($values);

        if ($valid) {
            // Store the last inserted id as groupId
            $group_id = $this->groupModel->create($values['name']);
            $user_id = $this->userSession->getId();
            if ($group_id !== false) {
                // Add current user as owner of the group
                $ownerError = $this->groupOwnerModel->addOwner($group_id, $user_id);
                if ($ownerError !== false) {
                    // If checkbox has been checked, add current user as member of this new group
                    if (isset($values['addOwnerToMembersList'])) {
                        $this->groupMemberModel->addUser($group_id, $user_id);
                    }
                    $this->flash->success(t('Group created successfully.'));
                    return $this->response->redirect($this->helper->url->to('GroupOwnersListController', 'index', array('plugin' => 'Group_owners')), true);
                }
            } else {
                $this->flash->failure(t('Unable to create your group.'));
            }
        }

        return $this->show($values, $errors);
    }
}
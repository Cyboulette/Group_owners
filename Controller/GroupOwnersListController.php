<?php

namespace Kanboard\Plugin\Group_owners\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\UserModel;
use Kanboard\Plugin\Group_owners\Model\GroupOwnerModel;
use Kanboard\Model\GroupModel;

class GroupOwnersListController extends BaseController {

    public function index() {
        $search = $this->request->getStringParam('search');
        $subqueryMember = $this->db->table(GroupMemberModel::TABLE)
            ->columns(GroupMemberModel::TABLE.'.group_id')
            ->eq(GroupMemberModel::TABLE.'.user_id', $this->userSession->getId());
        $subqueryOwner = $this->db->table(GroupOwnerModel::TABLE)
            ->columns(GroupOwnerModel::TABLE.'.group_id')
            ->eq(GroupOwnerModel::TABLE.'.user_id', $this->userSession->getId());

        $query = $this->db->table(GroupModel::TABLE)
            ->columns(GroupModel::TABLE.'.id', GroupModel::TABLE.'.external_id', GroupModel::TABLE.'.name')
            ->beginOr()
            ->inSubquery(GroupModel::TABLE.'.id', $subqueryMember)
            ->inSubquery(GroupModel::TABLE.'.id', $subqueryOwner)
            ->closeOr()
            ->subquery('SELECT COUNT(*) FROM '.GroupMemberModel::TABLE.' WHERE group_id='.GroupModel::TABLE.'.id', 'nb_users');

        // Find groups where user is owner
        $groupsUserIsOwner = $this->groupOwnerModel->getGroupsIds($this->userSession->getId());
        $groupsUserIsMember = $subqueryMember->findAllByColumn('user_id');

        if ($search !== '') {
            $query->ilike('groups.name', '%'.$search.'%');
        }

        $paginator = $this->paginator
            ->setUrl('GroupOwnersListController', 'index', ['plugin' => 'Group_owners'])
            ->setMax(30)
            ->setOrder(GroupModel::TABLE.'.name')
            ->setQuery($query)
            ->calculate();

        $this->response->html($this->helper->layout->app('group_owners:group/index', array(
            'title' => t('Groups').' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
            'values' => array(
                'search' => $search,
            ),
            'groupsUserIsOwner' => $groupsUserIsOwner,
            'groupsUserIsMember' => $groupsUserIsMember,
            'isPlatformAdmin' => $this->userSession->isAdmin()
        )));
    }

    public function users() {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->groupModel->getById($group_id);
        if ($group === null) {
            throw new AccessForbiddenException();
        }
        $isOwner = $this->groupOwnerModel->isOwner($group['id'], $this->userSession->getId());
        if (!$isOwner) {
            throw new AccessForbiddenException();
        }

        $subqueryMember = $this->db->table(GroupMemberModel::TABLE)
            ->columns(GroupMemberModel::TABLE.'.user_id')
            ->eq(GroupMemberModel::TABLE.'.group_id', $group_id);
        $subqueryOwner = $this->db->table(GroupOwnerModel::TABLE)
            ->columns(GroupOwnerModel::TABLE.'.user_id')
            ->eq(GroupOwnerModel::TABLE.'.group_id', $group_id);

        $query = $this->db->table(UserModel::TABLE)
            ->beginOr()
            ->inSubquery(UserModel::TABLE.'.id', $subqueryMember)
            ->inSubquery(UserModel::TABLE.'.id', $subqueryOwner)
            ->closeOr();

        $paginator = $this->paginator
            ->setUrl('GroupOwnersListController', 'users', array('group_id' => $group_id, 'plugin' => 'Group_owners'))
            ->setMax(30)
            ->setOrder(UserModel::TABLE.'.username')
            ->setQuery($query)
            ->calculate();

        $ownersIds = $this->groupOwnerModel->getOwnersIds($group_id);
        $membersIds = $this->groupOwnerModel->getMembersIds($group_id);

        $this->response->html($this->helper->layout->app('Group_owners:group/users', array(
            'title' => t('Members of %s', $group['name']).' ('.$paginator->getTotal().')',
            'paginator' => $paginator,
            'group' => $group,
            'ownersIds' => $ownersIds,
            'membersIds' => $membersIds,
        )));
    }

    public function associate(array $values = array(), array $errors = array()) {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->groupModel->getById($group_id);
        $user_id = $this->request->getIntegerParam('user_id', -1);
        $type = $this->request->getStringParam('type');
        $isFromAdmin = $this->request->getStringParam('isFromAdmin');
        $usersList = $this->groupMemberModel->getNotMembers($group_id);
        $title = t('Add group member');

        if (empty($values)) {
            $values['group_id'] = $group_id;
            if ($user_id !== -1) {
                $user = $this->userModel->getById($user_id);
                $values['user_id'] = $user_id;
                $values['addedFromUserList'] = $user;
            }
        }

        if ($isFromAdmin === 'true') {
            $values['addUserAsOwner'] = true;
            $values['isFromAdmin'] = true;
            $usersList = $this->groupOwnerModel->getNotOwners($group_id);
            $title = t('Add group owner');
        }

        $this->response->html($this->template->render('Group_owners:group/associate', array(
            'users' => $this->userModel->prepareList($usersList),
            'group' => $group,
            'errors' => $errors,
            'values' => $values,
            'type' => $type,
            'title' => $title
        )));
    }

    public function addUser() {
        $values = $this->request->getValues();
        $errors = array();
        $type = isset($values['type']) ? $values['type'] : "";
        if (isset($values['isFromAdmin'])) {
            $type = 'addOwnership';
        }

        if (isset($values['group_id']) && isset($values['user_id'])) {
            $isOwner = $this->groupOwnerModel->isOwner($values['group_id'], $this->userSession->getId());
            if ($isOwner) {
                if ($type === "" || $type === "addMembership") {
                    if ($this->groupMemberModel->addUser($values['group_id'], $values['user_id'])) {
                        if (isset($values['addUserAsOwner'])) {
                            $this->groupOwnerModel->addOwner($values['group_id'], $values['user_id']);
                        }
                        $this->flash->success(t('Group member added successfully.'));
                        return $this->response->redirect($this->helper->url->to('GroupOwnersListController', 'users', array('group_id' => $values['group_id'], 'plugin' => 'Group_owners')), true);
                    } else {
                        $this->flash->failure(t('Unable to add group member.'));
                    }
                } elseif ($type === "addOwnership") {
                    if ($this->groupOwnerModel->addOwner($values['group_id'], $values['user_id'])) {
                        $this->flash->success(t('"Group owner" role added successfully.'));
                        return $this->response->redirect($this->helper->url->to('GroupOwnersListController', 'users', array('group_id' => $values['group_id'], 'plugin' => 'Group_owners')), true);
                    } else {
                        $this->flash->failure(t('Unable to add group owner.'));
                    }
                }
            } else {
                $errors['isOwner'] = t('Unable to add group member.').' '.t('You are not owner of this group.');
            }
        }

        return $this->associate($values, $errors);
    }

    public function dissociate(array $values = array(), array $errors = array()) {
        $group_id = $this->request->getIntegerParam('group_id');
        $user_id = $this->request->getIntegerParam('user_id');
        $type = $this->request->getStringParam('type');
        $group = $this->groupModel->getById($group_id);
        $user = $this->userModel->getById($user_id);

        $this->response->html($this->template->render('Group_owners:group/dissociate', array(
            'group' => $group,
            'user' => $user,
            'type' => $type
        )));
    }

    public function removeUser() {
        $this->checkCSRFParam();
        $group_id = $this->request->getIntegerParam('group_id');
        $user_id = $this->request->getIntegerParam('user_id');
        $type = $this->request->getStringParam('type');

        $isOwner = $this->groupOwnerModel->isOwner($group_id, $this->userSession->getId());
        if (!$isOwner) {
            $this->flash->failure(t('Unable to remove this user from the group.').' '.t('You are not owner of this group.'));
        } else {
            if ($type === "removeOwnership") {
                if ($this->groupOwnerModel->removeOwner($group_id, $user_id)) {
                    $this->flash->success(t('"Group owner" role for this user removed successfully from this group.'));
                } else {
                    $this->flash->failure(t('Unable to remove the "group owner" role for this user from the group.'));
                }
            } else {
                if ($this->groupMemberModel->removeUser($group_id, $user_id)) {
                    $this->flash->success(t('User removed successfully from this group.'));
                } else {
                    $this->flash->failure(t('Unable to remove this user from the group.'));
                }
            }
        }

        $this->response->redirect($this->helper->url->to('GroupOwnersListController', 'users', array('group_id' => $group_id, 'plugin' => 'Group_owners')), true);
    }

    /**
     * Confirmation dialog to remove a group
     *
     * @access public
     */
    public function confirm()
    {
        $group_id = $this->request->getIntegerParam('group_id');
        $group = $this->groupModel->getById($group_id);

        $this->response->html($this->template->render('Group_owners:group/remove', array(
            'group' => $group,
        )));
    }

    /**
     * Remove a group
     *
     * @access public
     */
    public function remove()
    {
        $this->checkCSRFParam();
        $group_id = $this->request->getIntegerParam('group_id');
        $isOwner = $this->groupOwnerModel->isOwner($group_id, $this->userSession->getId());

        if ($isOwner) {
            if ($this->groupModel->remove($group_id)) {
                $this->flash->success(t('Group removed successfully.'));
            } else {
                $this->flash->failure(t('Unable to remove this group.'));
            }
        } else {
            $this->flash->failure(t('Unable to remove this group.').' '.t('You are not owner of this group.'));
        }

        $this->response->redirect($this->helper->url->to('GroupOwnersListController', 'index', array('plugin' => 'Group_owners')), false);
    }
}

<div class="dropdown">
    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong>#<?= $user['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <?php if ($user['isOwner'] && !$this->user->isCurrentUser($user['id'])): ?>
            <li>
                <?= $this->modal->medium('trash', t('Remove "group owner" role for this user'), 'GroupOwnersListController', 'dissociate', array('group_id' => $user['group_id'], 'user_id' => $user['id'], 'type' => 'removeOwnership', 'plugin' => 'Group_owners')) ?>
            </li>
        <?php endif; ?>

        <?php if (!$user['isOwner'] && $user['isMember']): ?>
            <li>
                <?= $this->modal->medium('plus', t('Add the role "group owner" for this user'), 'GroupOwnersListController', 'associate', array('group_id' => $user['group_id'], 'user_id' => $user['id'], 'type' => 'addOwnership', 'plugin' => 'Group_owners')) ?>
            </li>
        <?php endif; ?>

        <?php if ($user['isOwner'] && !$user['isMember']): ?>
            <li>
                <?= $this->modal->medium('plus', t('Add the role "group member" for this user'), 'GroupOwnersListController', 'associate', array('group_id' => $user['group_id'], 'user_id' => $user['id'], 'type' => 'addMembership', 'plugin' => 'Group_owners')) ?>
            </li>
        <?php endif; ?>

        <?php if($user['isMember']): ?>
            <li>
                <?= $this->modal->medium('trash', t('Remove "group member" role for this user'), 'GroupOwnersListController', 'dissociate', array('group_id' => $user['group_id'], 'user_id' => $user['id'], 'plugin' => 'Group_owners')) ?>
            </li>
        <?php endif; ?>
    </ul>
</div>

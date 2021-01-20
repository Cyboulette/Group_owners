<div class="dropdown">
    <?php if($group['currentUserIsOwner'] || $isPlatformAdmin): ?>
        <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong>#<?= $group['id'] ?> <i class="fa fa-caret-down"></i></strong></a>
    <ul>
        <li><?= $this->modal->medium('plus', t('Add group member'), 'GroupOwnersListController', 'associate', array('plugin' => 'Group_owners' ,'group_id' => $group['id'])) ?></li>
        <li><?= $this->url->icon('users', t('Members'), 'GroupOwnersListController', 'users', array('plugin' => 'Group_owners', 'group_id' => $group['id'])) ?></li>
        <!-- TODO
        <li><?= $this->modal->medium('edit', t('Edit'), 'GroupModificationController', 'show', array('group_id' => $group['id'])) ?></li>
        <li><?= $this->modal->confirm('trash-o', t('Remove'), 'GroupListController', 'confirm', array('group_id' => $group['id'])) ?></li>
        !-->
    </ul>
    <?php else: ?>
        <strong>#<?= $group['id'] ?></strong>
    <?php endif;?>
</div>
<div class="page-header">
    <h2><?= t('Remove user from group "%s"', $group['name']) ?></h2>
</div>
<div class="confirm">
    <?php if(isset($type) && $type === "removeOwnership"): ?>
        <p class="alert alert-info"><?= t('Do you really want to remove the "group owner" role for the user "%s" from the group "%s"?', $user['name'] ?: $user['username'], $group['name']) ?></p>
    <?php else: ?>
        <p class="alert alert-info"><?= t('Do you really want to remove the user "%s" from the group "%s"?', $user['name'] ?: $user['username'], $group['name']) ?></p>
    <?php endif; ?>

    <?= $this->modal->confirmButtons(
        'GroupOwnersListController',
        'removeUser',
        array('group_id' => $group['id'], 'user_id' => $user['id'], 'type' => $type, 'plugin' => 'Group_owners')
    ) ?>
</div>

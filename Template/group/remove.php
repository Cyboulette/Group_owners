<div class="page-header">
    <h2><?= t('Remove group') ?></h2>
</div>
<div class="confirm">
    <?php if($group != null): ?>
        <p class="alert alert-info"><?= t('Do you really want to remove this group: "%s"?', $group['name']) ?></p>

        <?= $this->modal->confirmButtons(
            'GroupOwnersListController',
            'remove',
            array('plugin' => 'Group_owners', 'group_id' => $group['id'])
        ) ?>
    <?php else: ?>
        <p class="alert alert-error"><?= t('Unable to find this group.') ?></p>
    <?php endif; ?>
</div>

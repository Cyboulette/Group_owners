<div class="page-header">
    <h2><?= $title . ' "' . $group['name'].'"' ?></h2>
</div>

<?php if (isset($errors['isOwner'])): ?>
    <p class="alert alert-error"><?= $this->text->e($errors['isOwner']) ?></p>
<?php endif ?>

<form method="post" action="<?= $this->url->href('GroupOwnersListController', 'addUser', array('group_id' => $group['id'], 'plugin' => 'Group_owners')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>
    <?= $this->form->hidden('group_id', $values) ?>
    <?= $this->form->hidden('isFromAdmin', $values) ?>

    <?php if($type === ''): ?>
        <?php if (empty($users)): ?>
            <p class="alert"><?= t('There is no user available.') ?></p>
            <div class="form-actions">
                <?= $this->url->link(t('Close this window'), 'GroupOwnersListController', 'index', array('plugin' => 'Group_owners'), false, 'btn js-modal-close') ?>
            </div>
        <?php else: ?>
            <?= $this->form->label(t('User'), 'user_id') ?>
            <?= $this->app->component('select-dropdown-autocomplete', array(
                'name' => 'user_id',
                'items' => $users,
                'defaultValue' => isset($values['user_id']) ? $values['user_id'] : key($users),
            )) ?>

            <br>
            <?= $this->form->checkbox('addUserAsOwner', t('Add this user as owner of this group'), true, isset($values['addUserAsOwner'])) ?>
            <?= $this->modal->submitButtons() ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($type === 'addMembership'): ?>
        <input type="hidden" name="user_id" value="<?= e($values['addedFromUserList']['id']) ?>">
        <input type="hidden" name="type" value="<?= e($type) ?>">
        <?= $this->user->getFullName($values['addedFromUserList']) ?> <?= t('will be added as member of this group.') ?>
        <?= $this->modal->submitButtons() ?>
    <?php endif; ?>

    <?php if ($type === 'addOwnership'): ?>
        <input type="hidden" name="user_id" value="<?= e($values['addedFromUserList']['id']) ?>">
        <input type="hidden" name="type" value="<?= e($type) ?>">
        <?= $this->user->getFullName($values['addedFromUserList']) ?> <?= t('will be added as owner of this group.') ?>
        <?= $this->modal->submitButtons() ?>
    <?php endif; ?>
</form>
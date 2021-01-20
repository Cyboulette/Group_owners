<div class="page-header">
    <h2><?= t('New group') ?></h2>
</div>

<form method="post" action="<?= $this->url->href('GroupOwnersCreationController', 'save', array('plugin' => 'Group_owners')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="191"')) ?>

    <?= $this->form->checkbox('addOwnerToMembersList', t('Add me as member of this group'), true, isset($values['addOwnerToMembersList'])) ?>

    <?= $this->modal->submitButtons() ?>
</form>

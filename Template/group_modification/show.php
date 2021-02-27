<div class="page-header">
    <h2><?= t('Edit group') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('GroupOwnersModificationController', 'save', array('plugin' => 'Group_owners')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->hidden('id', $values) ?>
    <?= $this->form->hidden('external_id', $values) ?>

    <?= $this->form->label(t('Name'), 'name') ?>
    <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="191"')) ?>

    <?= $this->modal->submitButtons() ?>
</form>

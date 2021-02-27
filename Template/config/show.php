<div class="page-header">
    <h2><?= t('GroupOwners plugin settings') ?></h2>
</div>
<form method="post" action="<?= $this->url->href('GroupOwnersConfigController', 'save', array('plugin' => 'Group_owners')) ?>" autocomplete="off">
    <?= $this->form->csrf() ?>

    <?= $this->form->checkbox('display_group_owners_icon', t('Display icon on application dropdown'), true, $checked) ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
    </div>
</form>
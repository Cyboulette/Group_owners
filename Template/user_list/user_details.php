<div class="table-list-details table-list-details-with-icons">
    <?php if($user['isOwner']): ?>
        <span class="table-list-category">
            <?= t('Group Owner') ?>
        </span>
    <?php endif; ?>

    <?php if($user['isMember']): ?>
        <span class="table-list-category">
            <?= t('Group Member') ?>
        </span>
    <?php endif; ?>

    <?php if (! empty($user['name'])): ?>
        <span><?= $this->text->e($user['username']) ?></span>
    <?php endif ?>

    <?php if (! empty($user['email'])): ?>
        <span><a href="mailto:<?= $this->text->e($user['email']) ?>"><?= $this->text->e($user['email']) ?></a></span>
    <?php endif ?>
</div>

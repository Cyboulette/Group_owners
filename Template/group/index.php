<div class="page-header">
    <ul>
        <li><?= $this->modal->medium('user-plus', t('New group'), 'GroupOwnersCreationController', 'show', array('plugin' => 'Group_owners')) ?></li>
    </ul>
</div>

<div class="margin-bottom">
    <form method="get" action="<?= $this->url->dir() ?>" class="search">
        <?= $this->form->hidden('controller', array('controller' => 'GroupOwnersListController')) ?>
        <?= $this->form->hidden('action', array('action' => 'index')) ?>
        <?= $this->form->hidden('plugin', array('plugin' => 'Group_owners')) ?>
        <?= $this->form->text('search', $values, array(), array('placeholder="'.t('Search').'"', 'aria-label="'.t('Search').'"')) ?>
    </form>
</div>

<?php if ($paginator->isEmpty()): ?>
    <?php if (isset($values['search'])) : ?>
        <p class="alert"><?= t('No groups are corresponding to your research.') ?></p>
    <?php else: ?>
        <p class="alert"><?= t('You are not member or not owner of any groups.') ?></p>
    <?php endif ?>
<?php else: ?>
    <div class="table-list">
        <div class="table-list-header">
            <div class="table-list-header-count">
                <?php if ($paginator->getTotal() > 1): ?>
                    <?= t('%d groups', $paginator->getTotal()) ?>
                <?php else: ?>
                    <?= t('%d group', $paginator->getTotal()) ?>
                <?php endif ?>
            </div>
            <div class="table-list-header-menu">
                <div class="dropdown">
                    <a href="#" class="dropdown-menu dropdown-menu-link-icon"><strong><?= t('Sort') ?> <i class="fa fa-caret-down"></i></strong></a>
                    <ul>
                        <li>
                            <?= $paginator->order(t('Group ID'), \Kanboard\Model\GroupModel::TABLE.'.id') ?>
                        </li>
                        <li>
                            <?= $paginator->order(t('Name'), \Kanboard\Model\GroupModel::TABLE.'.name') ?>
                        </li>
                        <li>
                            <?= $paginator->order(t('External ID'), \Kanboard\Model\GroupModel::TABLE.'.external_id') ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <?php foreach ($paginator->getCollection() as $group):
            $group['currentUserIsOwner'] = in_array($group['id'], $groupsUserIsOwner);
            $group['currentUserIsMember'] = in_array($group['id'], $groupsUserIsMember); ?>
            <div class="table-list-row table-border-left">
            <div class="table-list-title">
                <div>
                    <?= $this->render('Group_owners:group/dropdown', array('group' => $group, 'isPlatformAdmin' => $isPlatformAdmin)) ?>
                    <?php if($group['currentUserIsOwner'] || $isPlatformAdmin): ?>
                        <?= $this->url->link($this->text->e($group['name']), 'GroupOwnersListController', 'users', array('group_id' => $group['id'], 'plugin' => 'Group_owners')) ?>
                    <?php else: ?>
                        <?= $this->text->e($group['name']) ?>
                    <?php endif; ?>
                </div>
                <?php if($group['currentUserIsOwner']): ?>
                    <span class="table-list-category" style="margin-right: 10px;">
                        <?= t('Group Owner') ?>
                    </span>
                <?php endif; ?>
                <?php if($group['currentUserIsMember']): ?>
                    <span class="table-list-category">
                        <?= t('Group Member') ?>
                    </span>
                <?php endif; ?>
            </div>

                <div class="table-list-details">
                    <ul>
                        <?php if ($group['nb_users'] > 1): ?>
                            <li><?= t('%d users', $group['nb_users']) ?></li>
                        <?php else: ?>
                            <li><?= t('%d user', $group['nb_users']) ?></li>
                        <?php endif ?>

                        <?php if (! empty($group['external_id'])): ?>
                            <li><?= $this->text->e($group['external_id']) ?></li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        <?php endforeach ?>
    </div>

    <?= $paginator ?>
<?php endif ?>

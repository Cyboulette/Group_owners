<section id="main">
    <div class="page-header">
        <ul>
            <li><?= $this->url->icon('users', t('Manage my groups'), 'GroupOwnersListController', 'index', array('plugin' => 'Group_owners')) ?></li>
            <li><?= $this->modal->medium('plus', t('Add group member'), 'GroupOwnersListController', 'associate', array('group_id' => $group['id'], 'plugin' => 'Group_owners')) ?></li>
            <li><?= $this->modal->medium('edit', t('Edit'), 'GroupOwnersModificationController', 'show', array('plugin' => 'Group_owners', 'group_id' => $group['id'])) ?></li>
            <li><?= $this->modal->confirm('trash-o', t('Remove'), 'GroupOwnersListController', 'confirm', array('plugin' => 'Group_owners', 'group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <div class="margin-bottom">
        <form method="get" action="<?= $this->url->dir() ?>" class="search">
            <?= $this->form->hidden('controller', array('controller' => 'GroupOwnersListController')) ?>
            <?= $this->form->hidden('action', array('action' => 'users')) ?>
            <?= $this->form->hidden('plugin', array('plugin' => 'Group_owners')) ?>
            <?= $this->form->hidden('group_id', array('group_id' => $group['id'])) ?>
            <?= $this->form->text('search', $values, array(), array('placeholder="'.t('Search').'"')) ?>
        </form>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <?php if(isset($values['search']) && !empty($values['search'])): ?>
            <p class="alert"><?= t('There is no user in this group matching the search.') ?></p>
        <?php else: ?>
            <p class="alert"><?= t('There is no user in this group.') ?></p>
        <?php endif; ?>
    <?php else: ?>
        <div class="table-list">
            <?= $this->render('user_list/header', array('paginator' => $paginator)) ?>
            <?php foreach ($paginator->getCollection() as $user):
                $user['group_id'] = $group['id'];
                $user['isOwner'] = in_array($user['id'], $ownersIds);
                $user['isMember'] = in_array($user['id'], $membersIds);
            ?>
                <div class="table-list-row table-border-left">
                    <div>
                        <?= $this->render('Group_owners:group/user_dropdown', array('user' => $user)) ?>
                        <span class="table-list-title <?= $user['is_active'] == 0 ? 'status-closed' : '' ?>">
                            <?= $this->avatar->small(
                                $user['id'],
                                $user['username'],
                                $user['name'],
                                $user['email'],
                                $user['avatar_path'],
                                'avatar-inline'
                            ) ?>
                            <?php if ($this->user->isAdmin()): ?>
                                <?= $this->url->link($this->text->e($user['name'] ?: $user['username']), 'UserViewController', 'show', array('user_id' => $user['id'])) ?>
                            <?php else: ?>
                                <?= $this->text->e($user['name'] ?: $user['username']) ?>
                            <?php endif; ?>
                        </span>
                    </div>

                    <?= $this->render('Group_owners:user_list/user_details', array(
                        'user' => $user,
                    )) ?>
                </div>
            <?php endforeach ?>
        </div>

        <?= $paginator ?>
    <?php endif ?>
</section>
<section id="main">
    <div class="page-header">
        <ul>
            <li><?= $this->url->icon('users', t('Manage my groups'), 'GroupOwnersListController', 'index', array('plugin' => 'Group_owners')) ?></li>
            <li><?= $this->modal->medium('plus', t('Add group member'), 'GroupOwnersListController', 'associate', array('group_id' => $group['id'], 'plugin' => 'Group_owners')) ?></li>
        </ul>
    </div>
    <div class="margin-bottom"></div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('There is no user in this group.') ?></p>
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
                            <?= $this->text->e($user['name'] ?: $user['username']) ?>
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
<?php

namespace Kanboard\Plugin\Group_owners\Schema;

use PDO;

const VERSION = 1;

function version_1(PDO $pdo)
{
    $pdo->exec("
        CREATE TABLE group_has_owners (
            group_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            FOREIGN KEY(group_id) REFERENCES groups(id) ON DELETE CASCADE,
            FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
            UNIQUE(group_id, user_id)
        )
    ");
}
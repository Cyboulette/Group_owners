<?php

namespace Kanboard\Plugin\Group_owners\Model;

use Kanboard\Core\Base;
use Kanboard\Model\GroupMemberModel;
use Kanboard\Model\UserModel;

/**
 * Group Owner Model
 *
 * @package  Kanboard\Plugin\Group_owners\Model
 * @author   Cyboulette
 */
class GroupOwnerModel extends Base {
    /**
     * SQL table name
     *
     * @var string
     */
    const TABLE = 'group_has_owners';

    /**
     * Get query to fetch all users
     *
     * @access public
     * @param  integer $group_id
     * @return \PicoDb\Table
     */
    public function getQuery($group_id) {
        return $this->db->table(self::TABLE)
            ->join(UserModel::TABLE, 'id', 'user_id')
            ->eq('group_id', $group_id);
    }

    /**
     * Get all users
     *
     * @access public
     * @param  integer $group_id
     * @return array
     */
    public function getMembers($group_id) {
        return $this->getQuery($group_id)->findAll();
    }

    /**
     * Get all groups ids for a given user which is owner
     *
     * @access public
     * @param  integer $user_id
     * @return array
     */
    public function getGroupsIds($user_id) {
        return $this->db->table(self::TABLE)
            ->eq(self::TABLE.'.user_id', $user_id)
            ->findAllByColumn(self::TABLE.'.group_id');
    }

    /**
     * Get all owners of a given group
     *
     * @access public
     * @param integer $group_id
     * @return array
     */
    public function getOwnersIds($group_id) {
        return $this->db->table(self::TABLE)
            ->eq(self::TABLE.'.group_id', $group_id)
            ->findAllByColumn(self::TABLE.'.user_id');
    }

    /**
     * Get all members of a given group
     *
     * @access public
     * @param integer $group_id
     * @return array
     */
    public function getMembersIds($group_id) {
        return $this->db->table(GroupMemberModel::TABLE)
            ->eq(GroupMemberModel::TABLE.'.group_id', $group_id)
            ->findAllByColumn(GroupMemberModel::TABLE.'.user_id');
    }

    /**
     * Add given user as owner of given group
     *
     * @access public
     * @param integer $group_id
     * @param integer $user_id
     * @return boolean success
     */
    public function addOwner($group_id, $user_id) {
        return $this->db->table(self::TABLE)->insert(array(
            'group_id' => $group_id,
            'user_id' => $user_id,
        ));
    }

    /**
     * Remove given user as owner of given group
     *
     * @access public
     * @param integer $group_id
     * @param integer $user_id
     * @return boolean success
     */
    public function removeOwner($group_id, $user_id) {
        return $this->db->table(self::TABLE)
            ->eq('group_id', $group_id)
            ->eq('user_id', $user_id)
            ->remove();
    }

    /**
     * Check if given user is owner of given group
     * @param $group_id
     * @param $user_id
     * @return boolean
     */
    public function isOwner($group_id, $user_id) {
        return $this->userSession->isAdmin() || $this->db->table(self::TABLE)
            ->eq(self::TABLE.'.group_id', $group_id)
            ->eq(self::TABLE.'.user_id', $user_id)
            ->count() > 0;
    }

    /**
     * Get all not owners
     *
     * @access public
     * @param  integer $group_id
     * @return array
     */
    public function getNotOwners($group_id)
    {
        $subquery = $this->db->table(self::TABLE)
            ->columns('user_id')
            ->eq('group_id', $group_id);

        return $this->db->table(UserModel::TABLE)
            ->notInSubquery('id', $subquery)
            ->eq('is_active', 1)
            ->findAll();
    }
}

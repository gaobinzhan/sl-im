<?php

namespace App\Model\Dao;

use App\Model\Entity\GroupRelation;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class GroupRelationDao
 * @package App\Model\Dao
 * @Bean()
 */
class GroupRelationDao
{
    /**
     * @Inject()
     * @var GroupRelation
     */
    protected $groupRelationEntity;

    public function createGroupRelation(array $data)
    {
        return $this->groupRelationEntity::insertGetId($data);
    }

    public function findGroupRelationByGroupId(int $groupId)
    {
        return $this->groupRelationEntity::whereNull('deleted_at')
            ->where('group_id', '=', $groupId)
            ->get();
    }

    public function getGroupRelationUserIdsById(int $groupId)
    {
        return $this->groupRelationEntity::whereNull('deleted_at')
            ->where('group_id', '=', $groupId)
            ->select('user_id')
            ->get();
    }

    public function checkIsGroupRelation(int $userId, int $groupId)
    {
        return $this->groupRelationEntity::whereNull('deleted_at')
            ->where('user_id', '=', $userId)
            ->where('group_id', '=', $groupId)
            ->first();
    }

    public function getGroupRelationCountByGroupId(int $groupId)
    {
        return $this->groupRelationEntity::whereNull('deleted_at')
            ->where('group_id', '=', $groupId)
            ->count();
    }

    public function getGroupRelationByUserId(int $userId)
    {
        return $this->groupRelationEntity::whereNull('deleted_at')
            ->where('user_id', '=', $userId)
            ->get();
    }
}

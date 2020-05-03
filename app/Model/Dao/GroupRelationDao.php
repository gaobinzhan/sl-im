<?php

namespace App\Model\Dao;

use App\Model\Entity\GroupRelation;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\Query\Builder;

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

    public function getSelfGroupRelation(array $condition, int $userId, int $page, int $limit)
    {
        return $this->groupRelationEntity::whereNull('group_relation.deleted_at')
            ->leftJoin('group', 'group.group_id', '=', 'group_relation.group_id')
            ->leftJoin('user', 'user.user_id', '=', 'group.user_id')
            ->where('group_relation.user_id', $userId)
            ->where(function (Builder $builder) use ($condition) {
                !empty($condition['group_name']) && $builder->where('group.group_name', 'like', "%{$condition['group_name']}%");
            })
            ->paginate($page, $limit, [
                'group_relation.group_relation_id',
                'group_relation.created_at',
                'group.group_id',
                'group.size',
                'group.avatar',
                'group.group_name',
                'group.introduction',
                'user.email'
            ]);
    }
}

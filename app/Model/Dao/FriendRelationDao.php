<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Dao;

use App\Model\Entity\FriendRelation;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\Query\Builder;

/**
 * Class FriendRelationDao
 * @package App\Model\Dao
 * @Bean()
 */
class FriendRelationDao
{

    /**
     * @Inject()
     * @var FriendRelation
     */
    protected $friendRelationEntity;

    public function getFriendRelationByFriendGroupIds(array $friendGroupIds)
    {
        return $this->friendRelationEntity::whereNull('deleted_at')
            ->whereIn('friend_group_id', $friendGroupIds)
            ->get();
    }

    public function checkIsFriendRelation(int $userId, int $friendId)
    {
        return $this->friendRelationEntity::whereNull('deleted_at')
            ->where('user_id', '=', $userId)
            ->where('friend_id', '=', $friendId)
            ->first();
    }

    public function createFriendRelation(array $data)
    {
        return $this->friendRelationEntity::insertGetId($data);
    }

    public function changeFriendRelationById(int $id, array $data)
    {
        return $this->friendRelationEntity::whereNull('deleted_at')
            ->where('friend_relation_id', '=', $id)
            ->update($data);
    }


    public function getFriendIdsByUserId(int $userId)
    {
        return $this->friendRelationEntity::whereNull('deleted_at')
            ->where('user_id', '=', $userId)
            ->select('friend_id')
            ->get();
    }

    public function getFriendRelationByUserId(int $userId)
    {
        return $this->friendRelationEntity::whereNull('deleted_at')
            ->where('user_id', '=', $userId)
            ->get();
    }

    public function getRelationList(int $userId, int $page, int $limit, array $condition)
    {
        return $this->friendRelationEntity::whereNull('friend_relation.deleted_at')
            ->leftJoin('friend_group', 'friend_group.friend_group_id', '=', 'friend_relation.friend_group_id')
            ->leftJoin('user', 'user.user_id', '=', 'friend_relation.friend_id')
            ->where('friend_relation.user_id', $userId)
            ->where(function (Builder $builder) use ($condition) {
                !empty($condition['username']) && $builder->where('user.username', 'like', "%{$condition['username']}%");
                !empty($condition['email']) && $builder->where('user.email', $condition['email']);
                !empty($condition['friend_id']) && $builder->where('friend_relation.friend_id', $condition['friend_id']);
                !empty($condition['friend_group_id']) && $builder->where('friend_relation.friend_group_id', $condition['friend_group_id']);
            })
            ->paginate($page, $limit);
    }
}

<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Dao;


use App\Model\Entity\GroupChatHistory;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class GroupChatHistoryDao
 * @package App\Model\Dao
 * @Bean()
 */
class GroupChatHistoryDao
{
    /**
     * @Inject()
     * @var GroupChatHistory
     */
    protected $groupChatHistoryEntity;

    public function createGroupChatHistory(array $data)
    {
        return $this->groupChatHistoryEntity::insertGetId($data);
    }

    public function findGroupChatHistoryById(int $id)
    {
        return $this->groupChatHistoryEntity::whereNull('deleted_at')->find($id);
    }

    public function getChatHistory(int $toGroupId, int $page, int $size)
    {
        return $this->groupChatHistoryEntity::whereNull('deleted_at')
            ->where('to_group_id','=',$toGroupId)
            ->orderBy('created_at','desc')
            ->paginate($page, $size);
    }
}

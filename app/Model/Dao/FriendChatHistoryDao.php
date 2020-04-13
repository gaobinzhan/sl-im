<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Dao;

use App\Model\Entity\FriendChatHistory;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\Eloquent\Builder;

/**
 * Class FriendChatHistoryDao
 * @package App\Model\Dao
 * @Bean()
 */
class FriendChatHistoryDao
{
    /**
     * @Inject()
     * @var FriendChatHistory
     */
    protected $friendChatHistoryEntity;

    public function createFriendChatHistory(array $data)
    {
        return $this->friendChatHistoryEntity::insertGetId($data);
    }

    public function findFriendChatHistoryById(int $id)
    {
        return $this->friendChatHistoryEntity::whereNull('deleted_at')->find($id);
    }

    public function setFriendChatHistoryReceptionStateByMessageId(string $messageId, int $receptionState)
    {
        return $this->friendChatHistoryEntity::whereNull('deleted_at')
            ->where('message_id', '=', $messageId)
            ->update(['reception_state' => $receptionState]);
    }

    public function getUnreadMessageByToUserId(int $userId)
    {
        return $this->friendChatHistoryEntity::whereNull('deleted_at')
            ->where('to_user_id', '=', $userId)
            ->where('reception_state', '=', FriendChatHistory::NOT_RECEIVED)
            ->get();
    }

    public function getChatHistory(int $fromUserId, int $userId, int $page, int $size)
    {
        return $this->friendChatHistoryEntity::whereNull('deleted_at')
            ->where(function (Builder $builder) use ($fromUserId, $userId) {
                $builder->orWhere(function (Builder $builder) use ($fromUserId, $userId) {
                    $builder->where('from_user_id', '=', $fromUserId);
                    $builder->where('to_user_id', $userId);
                });
                $builder->orWhere(function (Builder $builder) use ($fromUserId, $userId) {
                    $builder->where('from_user_id', '=', $userId);
                    $builder->where('to_user_id', $fromUserId);
                });
            })
            ->paginate($page, $size);
    }
}

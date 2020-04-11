<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Dao;

use App\Model\Entity\FriendChatHistory;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

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
}

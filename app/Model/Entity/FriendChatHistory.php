<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 好友聊天记录
 * Class FriendChatHistory
 *
 * @since 2.0
 *
 * @Entity(table="friend_chat_history")
 */
class FriendChatHistory extends Model
{
    const NOT_RECEIVED = 0;
    const RECEIVED = 1;

    /**
     * 主键
     * @Id()
     * @Column(name="friend_chat_history_id", prop="friendChatHistoryId")
     *
     * @var int
     */
    private $friendChatHistoryId;

    /**
     * 唯一消息id
     *
     * @Column(name="message_id", prop="messageId")
     *
     * @var string
     */
    private $messageId;

    /**
     * 发送方
     *
     * @Column(name="from_user_id", prop="fromUserId")
     *
     * @var int
     */
    private $fromUserId;

    /**
     * 接收方
     *
     * @Column(name="to_user_id", prop="toUserId")
     *
     * @var int
     */
    private $toUserId;

    /**
     * 消息内容
     *
     * @Column()
     *
     * @var string
     */
    private $content;

    /**
     * 接收状态 0 未接收 1 接收
     *
     * @Column(name="reception_state", prop="receptionState")
     *
     * @var int
     */
    private $receptionState;

    /**
     *
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     *
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;

    /**
     * 删除时间 为NULL未删除
     *
     * @Column(name="deleted_at", prop="deletedAt")
     *
     * @var string|null
     */
    private $deletedAt;


    /**
     * @param int $friendChatHistoryId
     *
     * @return self
     */
    public function setFriendChatHistoryId(int $friendChatHistoryId): self
    {
        $this->friendChatHistoryId = $friendChatHistoryId;

        return $this;
    }

    /**
     * @param string $messageId
     *
     * @return self
     */
    public function setMessageId(string $messageId): self
    {
        $this->messageId = $messageId;

        return $this;
    }

    /**
     * @param int $fromUserId
     *
     * @return self
     */
    public function setFromUserId(int $fromUserId): self
    {
        $this->fromUserId = $fromUserId;

        return $this;
    }

    /**
     * @param int $toUserId
     *
     * @return self
     */
    public function setToUserId(int $toUserId): self
    {
        $this->toUserId = $toUserId;

        return $this;
    }

    /**
     * @param string $content
     *
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @param int $receptionState
     *
     * @return self
     */
    public function setReceptionState(int $receptionState): self
    {
        $this->receptionState = $receptionState;

        return $this;
    }

    /**
     * @param string|null $createdAt
     *
     * @return self
     */
    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @param string|null $deletedAt
     *
     * @return self
     */
    public function setDeletedAt(?string $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return int
     */
    public function getFriendChatHistoryId(): ?int
    {
        return $this->friendChatHistoryId;
    }

    /**
     * @return string
     */
    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    /**
     * @return int
     */
    public function getFromUserId(): ?int
    {
        return $this->fromUserId;
    }

    /**
     * @return int
     */
    public function getToUserId(): ?int
    {
        return $this->toUserId;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return int
     */
    public function getReceptionState(): ?int
    {
        return $this->receptionState;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return string|null
     */
    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

}

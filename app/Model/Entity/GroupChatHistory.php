<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 群聊天记录
 * Class GroupChatHistory
 *
 * @since 2.0
 *
 * @Entity(table="group_chat_history")
 */
class GroupChatHistory extends Model
{
    /**
     * 主键
     * @Id()
     * @Column(name="group_chat_history_id", prop="groupChatHistoryId")
     *
     * @var int
     */
    private $groupChatHistoryId;

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
     * 接收群
     *
     * @Column(name="to_group_id", prop="toGroupId")
     *
     * @var int
     */
    private $toGroupId;

    /**
     * 消息内容
     *
     * @Column()
     *
     * @var string
     */
    private $content;

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
     * @param int $groupChatHistoryId
     *
     * @return self
     */
    public function setGroupChatHistoryId(int $groupChatHistoryId): self
    {
        $this->groupChatHistoryId = $groupChatHistoryId;

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
     * @param int $toGroupId
     *
     * @return self
     */
    public function setToGroupId(int $toGroupId): self
    {
        $this->toGroupId = $toGroupId;

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
    public function getGroupChatHistoryId(): ?int
    {
        return $this->groupChatHistoryId;
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
    public function getToGroupId(): ?int
    {
        return $this->toGroupId;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
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

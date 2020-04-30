<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 验证码表
 * Class Verify
 *
 * @since 2.0
 *
 * @Entity(table="verify")
 */
class Verify extends Model
{
    const  DEFAULT_STATUS = 0;
    const  USED_STATUS = 1;

    /**
     * 主键
     * @Id()
     * @Column(name="verify_id", prop="verifyId")
     *
     * @var int
     */
    private $verifyId;

    /**
     * 被验证的对象
     *
     * @Column()
     *
     * @var string
     */
    private $object;

    /**
     * 验证码
     *
     * @Column()
     *
     * @var string
     */
    private $code;

    /**
     * 请求ip地址
     *
     * @Column()
     *
     * @var string
     */
    private $ip;

    /**
     * 是否使用 0 未使用 1 使用
     *
     * @Column()
     *
     * @var int
     */
    private $status;

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
     * @param int $verifyId
     *
     * @return self
     */
    public function setVerifyId(int $verifyId): self
    {
        $this->verifyId = $verifyId;

        return $this;
    }

    /**
     * @param string $object
     *
     * @return self
     */
    public function setObject(string $object): self
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @param string $code
     *
     * @return self
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string $ip
     *
     * @return self
     */
    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return self
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;

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
    public function getVerifyId(): ?int
    {
        return $this->verifyId;
    }

    /**
     * @return string
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getIp(): ?string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
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

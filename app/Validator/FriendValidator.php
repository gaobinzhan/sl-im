<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Validator;

use Swoft\Validator\Annotation\Mapping\Enum;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\NotEmpty;
use Swoft\Validator\Annotation\Mapping\Required;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class FriendValidator
 * @package App\Validator
 * @Validator(name="FriendValidator")
 */
class FriendValidator
{
    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="分组名称不能为空")
     * @Length(max=30)
     * @var string
     */
    protected $friend_group_name = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="用户编号不能为空")
     * @var int
     */
    protected $user_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="接收者编号不能为空")
     * @var int
     */
    protected $receiver_id = '';

    /**
     * @IsInt(message="请先创建好友分组")
     * @Required()
     * @NotEmpty(message="请先创建好友分组")
     * @var int
     */
    protected $friend_group_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="接收者编号不能为空")
     * @var int
     */
    protected $to_user_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="发送者编号不能为空")
     * @var int
     */
    protected $from_user_id = '';


    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="申请类型不能为空")
     * @Enum(values={"friend","group"})
     * @var string
     */
    protected $application_type = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="申请理由不能为空")
     * @Length(max=255)
     * @var
     */
    protected $application_reason = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="申请编号不能为空")
     * @var int
     */
    protected $user_application_id = '';
}

<?php declare(strict_types=1);

namespace App\Validator;

use Swoft\Validator\Annotation\Mapping\Enum;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\NotEmpty;
use Swoft\Validator\Annotation\Mapping\Required;
use Swoft\Validator\Annotation\Mapping\Url;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class GroupValidator
 * @package App\Validator
 * @Validator(name="GroupValidator")
 */
class GroupValidator
{
    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="群编号不能为空")
     * @var int
     */
    protected $id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="用户编号不能为空")
     * @var int
     */
    protected $user_id = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="群昵称不能为空")
     * @Length(max=30)
     * @var string
     */
    protected $group_name = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="头像url不能为空")
     * @Url(message="url不合法")
     * @Length(max=255)
     * @var string
     */
    protected $avatar = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="群规模不能为空")
     * @Enum(values={"200","500","1000"})
     * @var string
     */
    protected $size = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="群介绍不能为空")
     * @Length(max=255)
     * @var string
     */
    protected $introduction = '';

    /**
     * @IsInt()
     * @Required()
     * @Enum(values={0,1})
     * @var int
     */
    protected $validation = 0;

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="群编号不能为空")
     * @var int
     */
    protected $group_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="群编号不能为空")
     * @var int
     */
    protected $to_group_id = '';

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

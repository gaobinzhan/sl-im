<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Validator;

use Swoft\Validator\Annotation\Mapping\AlphaDash;
use Swoft\Validator\Annotation\Mapping\Email;
use Swoft\Validator\Annotation\Mapping\Enum;
use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\NotEmpty;
use Swoft\Validator\Annotation\Mapping\Required;
use Swoft\Validator\Annotation\Mapping\Url;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class UserValidator
 * @package App\Validator
 * @Validator(name="UserValidator")
 */
class UserValidator
{

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
     * @NotEmpty(message="邮箱不能为空")
     * @Email(message="邮箱格式不正确")
     * @Length(min=8,max=50)
     * @var string
     */
    protected $email = '';


    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="用户昵称不能为空")
     * @Length(max=30)
     * @var string
     */
    protected $username = '';

    /**
     * @IsString()
     * @AlphaDash(message="必须是大小写字母、数字、短横 -、下划线 _")
     * @Required()
     * @NotEmpty(message="密码不能为空")
     * @Length(min=8,max=20)
     * @var string
     */
    protected $password = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="签名不能为空")
     * @Length(max=50)
     * @var string
     */
    protected $sign = '';

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
     * @Enum(values={0,1})
     * @var int
     */
    protected $status = 0;

    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @var string
     */
    protected $code = '';
}

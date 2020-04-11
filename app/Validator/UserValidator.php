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
     * @NotEmpty()
     * @var int
     */
    protected $user_id = '';


    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @Email()
     * @Length(min=8,max=50)
     * @var string
     */
    protected $email = '';


    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @Length(max=30)
     * @var string
     */
    protected $username = '';

    /**
     * @IsString()
     * @AlphaDash()
     * @Required()
     * @NotEmpty()
     * @Length(min=8,max=20)
     * @var string
     */
    protected $password = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @Length(max=50)
     * @var string
     */
    protected $sign = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @Url()
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
}

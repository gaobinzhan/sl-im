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
     * @NotEmpty()
     * @Length(max=30)
     * @var string
     */
    protected $friend_group_name = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty()
     * @var int
     */
    protected $user_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty()
     * @var int
     */
    protected $receiver_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty()
     * @var int
     */
    protected $friend_group_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty()
     * @var int
     */
    protected $to_user_id = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty()
     * @var int
     */
    protected $from_user_id = '';


    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @Enum(values={"friend","group"})
     * @var string
     */
    protected $application_type = '';

    /**
     * @IsString()
     * @Required()
     * @NotEmpty()
     * @Length(max=255)
     * @var
     */
    protected $application_reason = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty()
     * @var int
     */
    protected $user_application_id = '';
}

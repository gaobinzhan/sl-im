<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Validator;

use Swoft\Validator\Annotation\Mapping\IsInt;
use Swoft\Validator\Annotation\Mapping\IsString;
use Swoft\Validator\Annotation\Mapping\Length;
use Swoft\Validator\Annotation\Mapping\Max;
use Swoft\Validator\Annotation\Mapping\NotEmpty;
use Swoft\Validator\Annotation\Mapping\Validator;

/**
 * Class SearchValidator
 * @package App\Validator
 * @Validator(name="SearchValidator")
 */
class SearchValidator
{
    /**
     * @IsString()
     * @Required()
     * @NotEmpty(message="搜索关键字不能为空")
     * @Length(max=30)
     * @var string
     */
    protected $keyword = '';

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="页码不能为空")
     * @var int
     */
    protected $page = 1;

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="每页数量不能为空")
     * @Max(value=20)
     * @var int
     */
    protected $size = 20;

    /**
     * @IsInt()
     * @Required()
     * @NotEmpty(message="每页数量不能为空")
     * @Max(value=100)
     * @var int
     */
    protected $limit = 20;
}

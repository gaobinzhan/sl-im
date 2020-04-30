<?php declare(strict_types=1);


namespace Database\Migration;


use Swoft\Db\Schema\Blueprint;
use Swoft\Devtool\Annotation\Mapping\Migration;
use Swoft\Devtool\Migration\Migration as BaseMigration;

/**
 * Class Verify
 *
 * @since 2.0
 *
 * @Migration(time=20200430123855)
 */
class Verify extends BaseMigration
{
    const TABLE = 'verify';

    /**
     * @return void
     */
    public function up(): void
    {
        $this->schema->createIfNotExists(self::TABLE, function (Blueprint $blueprint) {
            $blueprint->comment('验证码表');
            $blueprint->increments('verify_id')->comment('主键');
            $blueprint->string('object', 50)->comment('被验证的对象');
            $blueprint->char('code', 4)->comment('验证码');
            $blueprint->string('ip', 15)->default('')->comment('请求ip地址');
            $blueprint->tinyInteger('status', false, true, 1)->default(0)->comment('是否使用 0 未使用 1 使用');
            $blueprint->timestamps();
//            $blueprint->tinyInteger('delete_flag', false, true, 1)->default(0)->comment('软删除 0正常 1删除');
            $blueprint->softDeletes()->comment('删除时间 为NULL未删除');
            $blueprint->index('verify_id');
            $blueprint->engine = 'Innodb';
            $blueprint->charset = 'utf8mb4';
        });

    }

    /**
     * @return void
     */
    public function down(): void
    {
        $this->schema->drop(self::TABLE);
    }
}

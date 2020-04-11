<?php declare(strict_types=1);


namespace Database\Migration;


use Swoft\Db\Schema\Blueprint;
use Swoft\Devtool\Annotation\Mapping\Migration;
use Swoft\Devtool\Migration\Migration as BaseMigration;

/**
 * Class GroupChatHistory
 *
 * @since 2.0
 *
 * @Migration(time=20200402152014)
 */
class GroupChatHistory extends BaseMigration
{
    const TABLE = 'group_chat_history';

    /**
     * @return void
     */
    public function up(): void
    {
        $this->schema->createIfNotExists(self::TABLE, function (Blueprint $blueprint) {
            $blueprint->comment('群聊天记录');
            $blueprint->increments('group_chat_history_id')->comment('主键');
            $blueprint->char('message_id',11)->comment('唯一消息id');
            $blueprint->integer('from_user_id', false, true, 11)->comment('发送方');
            $blueprint->integer('to_group_id', false, true, 11)->comment('接收群');
            $blueprint->longText('content')->comment('消息内容');
            $blueprint->timestamps();
//            $blueprint->tinyInteger('delete_flag', false, true, 1)->default(0)->comment('软删除 0正常 1删除');
            $blueprint->softDeletes()->comment('删除时间 为NULL未删除');
            $blueprint->index('message_id');
            $blueprint->index('from_user_id');
            $blueprint->index('to_group_id');
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

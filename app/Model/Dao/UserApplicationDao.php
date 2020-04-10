<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Dao;

use App\Model\Entity\UserApplication;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoft\Db\Eloquent\Builder;

/**
 * Class UserApplicationDao
 * @package App\Model\Dao
 * @Bean()
 */
class UserApplicationDao
{

    /**
     * @Inject()
     * @var UserApplication
     */
    protected $userApplicationEntity;

    public function createUserApplication($data)
    {
        return $this->userApplicationEntity::insertGetId($data);
    }

    public function getUnreadApplicationCount(int $userId)
    {
        return $this->userApplicationEntity::whereNull('deleted_at')
            ->where('read_state', 'eq', $this->userApplicationEntity::UN_READ)
            ->Where('receiver_id', '=', $userId)
            ->count();
    }

    public function getApplication(int $userId, int $page, int $size)
    {
        return $this->userApplicationEntity::whereNull('deleted_at')
            ->where(function (Builder $builder) use ($userId) {
                $builder->where('user_id', '=', $userId);
                $builder->orWhere('receiver_id', '=', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($page, $size);
    }

    public function changeApplicationReadStateByIdsAndReceiverId(array $ids, int $receiver_id, int $readState)
    {
        return $this->userApplicationEntity::whereNull('deleted_at')
            ->where('receiver_id', '=', $receiver_id)
            ->whereIn('user_application_id', $ids)
            ->update(['read_state' => $readState]);
    }

    public function changeApplicationStatusById(int $id, int $applicationStatus)
    {
        return $this->userApplicationEntity::whereNull('deleted_at')
            ->where('user_application_id', $id)
            ->update([
                'application_status' => $applicationStatus
            ]);
    }


    public function findUserApplicationById(int $id)
    {
        return $this->userApplicationEntity::whereNull('deleted_at')->find($id);
    }
}

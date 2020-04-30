<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Dao;

use App\Model\Entity\Verify;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;

/**
 * Class VerifyDao
 * @package App\Model\Dao
 * @Bean()
 */
class VerifyDao
{
    /**
     * @Inject()
     * @var Verify
     */
    protected $verifyEntity;

    public function getVerifyCountByObjectAndTime(string $object, string $start, string $end)
    {
        return $this->verifyEntity::whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->where('object', $object)
            ->count();
    }

    public function getVerifyCountByIpAndTime(string $ip, string $start, string $end)
    {
        return $this->verifyEntity::whereNull('deleted_at')
            ->whereBetween('created_at', [$start, $end])
            ->where('ip', $ip)
            ->count();
    }

    public function findVerifyByObjectDesc(string $object)
    {
        return $this->verifyEntity::whereNull('deleted_at')
            ->where('object', 'eq', $object)
            ->orderBy('created_at', 'desc')
            ->find();
    }

    public function createVerify(array $data)
    {
        return $this->verifyEntity::insertGetId($data);
    }
}

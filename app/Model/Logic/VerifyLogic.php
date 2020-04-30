<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Logic;

use App\ExceptionCode\ApiCode;
use App\Model\Dao\VerifyDao;
use App\Model\Entity\Verify;
use App\Model\Service\AliYunMailService;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Bean\Annotation\Mapping\Inject;
use Swoole\Coroutine;

/**
 * Class VerifyLogic
 * @package App\Model\Logic
 * @Bean()
 */
class VerifyLogic
{
    /**
     * @Inject()
     * @var VerifyDao
     */
    protected $verifyDao;

    public function sendMail(string $email)
    {
        $start = date('Y-m-d 00:00:00', time());
        $end = date('Y-m-d 23:59:59', time());

        $request = context()->getRequest();
        $ip = empty($request->getHeaderLine('x-real-ip')) ? $request->getServerParams()['remote_addr'] : $request->getHeaderLine('x-real-ip');

        $emailCount = $this->verifyDao->getVerifyCountByObjectAndTime($email, $start, $end);
        $ipCount = $this->verifyDao->getVerifyCountByIpAndTime($ip, $start, $end);


        if ($ipCount >= 10 || $emailCount >= 3) {
            throw new \Exception(null, ApiCode::MAIL_SENDING_LIMIT);
        }
        $code = rand(1000, 9999);

        $result = $this->createVerify($email, $code, $ip);
        if (!$result) throw new \Exception(null, ApiCode::MAIL_SEND_FAIL);
        Coroutine::create(function () use ($email, $code) {
            (new AliYunMailService())->sendMail($email, $code);
        });
        return $result;
    }

    public function enterVerify(string $object, string $code)
    {
        /** @var Verify $verifyInfo */
        $verifyInfo = $this->verifyDao->findVerifyByObjectDesc($object);

        if (!$verifyInfo) throw new \Exception(null, ApiCode::VERIFY_CODE_ERROR);

        if ($verifyInfo->getStatus() == Verify::USED_STATUS) throw new \Exception(null, ApiCode::VERiFY_CODE_USED);

        if ($verifyInfo->getCode() !== $code) throw new \Exception(null, ApiCode::VERIFY_CODE_ERROR);


        if ((time() - strtotime($verifyInfo->getCreatedAt())) >= 600) throw new \Exception(null, ApiCode::VERIFY_CODE_IS_INVALID);

        return $this->verifyDao->setVerifyCodeForUsedById($verifyInfo->getVerifyId());
    }

    public function createVerify(string $object, string $code, string $ip)
    {
        return $this->verifyDao->createVerify([
            'object' => $object,
            'code' => $code,
            'status' => Verify::DEFAULT_STATUS,
            'ip' => $ip
        ]);
    }
}

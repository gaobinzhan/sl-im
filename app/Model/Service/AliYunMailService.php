<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Model\Service;


use App\ExceptionCode\ApiCode;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoole\Coroutine;

/**
 * Class AliYunMailService
 * @package App\Model\Service
 * @Bean()
 */
class AliYunMailService
{
    private function checkParams()
    {
        $aliYunMailConfig = config('mail.aliYun');
        $accountName = $aliYunMailConfig['accountName'];
        $fromAlias = $aliYunMailConfig['fromAlias'];
        $subject = $aliYunMailConfig['subject'];
        $body = $aliYunMailConfig['body'];
        $version = $aliYunMailConfig['version'];
        $accessKeyId = $aliYunMailConfig['accessKeyId'];
        $regionId = $aliYunMailConfig['regionId'];
        $accessKeySecret = $aliYunMailConfig['accessKeySecret'];
        $host = $aliYunMailConfig['host'];

        if (empty($accountName) || empty($fromAlias) || empty($subject) || empty($body) || empty($version) || empty($accessKeyId) || empty($regionId) || empty($accessKeySecret) || empty($host)) {
            throw new \Exception(null, ApiCode::ERR_ALI_MAIL_CONFIG);
        }

        return [$accountName, $accessKeyId, $accessKeySecret, $fromAlias, $subject, $body, $version, $regionId, $host];
    }

    public function sendMail(string $mail, string $code)
    {
        [$accountName, $accessKeyId, $accessKeySecret, $fromAlias, $subject, $body, $version, $regionId, $host] = $this->checkParams();

        $data = [
            'Action' => 'SingleSendMail',
            'AccountName' => $accountName,
            'ReplyToAddress' => "true",
            'AddressType' => 1,
            'ToAddress' => $mail,
            'FromAlias' => $fromAlias,
            'Subject' => str_replace('{email}', $mail, $subject),
            'HtmlBody' => str_replace('{code}', $code, $body),
            'Format' => 'JSON',
            'Version' => $version,
            'AccessKeyId' => $accessKeyId,
            'SignatureMethod' => 'HMAC-SHA1',
            'Timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'SignatureVersion' => '1.0',
            'SignatureNonce' => md5(time()),
            'RegionId' => $regionId
        ];
        $data['Signature'] = $this->sign($data, $accessKeySecret);


        $client = new Coroutine\Http\Client($host, 443, true);

        $client->post('/', $data);

        $result = $client->getBody();
        $client->close();
        return $result;
    }


    private function sign($params, $accessKeySecret)
    {
        ksort($params);

        $stringToSign = 'POST&' . $this->percentEncode('/') . '&';

        $tmp = '';

        foreach ($params as $k => $param) $tmp .= '&' . $this->percentEncode($k) . '=' . $this->percentEncode($param);

        $tmp = trim($tmp, '&');

        $stringToSign = $stringToSign . $this->percentEncode($tmp);

        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $accessKeySecret . '&', TRUE));

        return $signature;
    }


    private function percentEncode($val)
    {
        $res = urlencode($val);
        $res = preg_replace('/\+/', '%20', $res);
        $res = preg_replace('/\*/', '%2A', $res);
        $res = preg_replace('/%7E/', '~', $res);
        return $res;
    }

    private function getPostHttpBody($param)
    {
        $str = "";
        foreach ($param as $k => $v) $str .= $k . '=' . urlencode($v) . '&';
        return substr($str, 0, -1);
    }
}

<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link     https://swoft.org
 * @document https://swoft.org/docs
 * @contact  group@swoft.org
 * @license  https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Common\WsMessage;
use App\ExceptionCode\ApiCode;
use Swoft\Error\Annotation\Mapping\ExceptionHandler;
use Swoft\Log\Helper\Log;
use Swoft\WebSocket\Server\Exception\Handler\AbstractMessageErrorHandler;
use Swoole\WebSocket\Frame;
use Throwable;
use function server;
use const APP_DEBUG;

/**
 * Class WsMessageExceptionHandler
 *
 * @since 2.0
 *
 * @ExceptionHandler(\Throwable::class)
 */
class WsMessageExceptionHandler extends AbstractMessageErrorHandler
{
    /**
     * @param Throwable $e
     * @param Frame $frame
     */
    public function handle(Throwable $e, Frame $frame): void
    {
        $message = sprintf('%s At %s line %d', $e->getMessage(), $e->getFile(), $e->getLine());

        Log::error('Ws server error(%s)', $message);


        // Debug is false
        if (!APP_DEBUG) {
            $message = $e->getMessage();
        }

        if (isset(ApiCode::$errorMessages[$e->getCode()])) {
            $message = ApiCode::$errorMessages[$e->getCode()];
        }

        $result = wsError($message, WsMessage::WS_MESSAGE_CMD_ERROR,['message_id' => $e->getMessage()]);
        server()->sendTo($frame->fd, $result);
    }
}

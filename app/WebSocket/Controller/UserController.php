<?php declare(strict_types=1);

namespace App\WebSocket\Controller;

use Swoft\WebSocket\Server\Annotation\Mapping\WsController;
use Swoft\WebSocket\Server\Annotation\Mapping\MessageMapping;

/**
 * Class UserController - This is an controller for handle websocket message request
 *
 * @WsController("user")
 */
class UserController{
    /**
     * @MessageMapping("ping")
     */
    public function index()
    {
        return WEBSOCKET_OPCODE_PONG;
    }
}

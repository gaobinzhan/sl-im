<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Common;


class WsMessage
{
    const WS_MESSAGE_TYPE_EVENT = 'event';
    const WS_MESSAGE_TYPE_MESSAGE = 'message';
    const EVENT_USER_STATUS = 'setUserStatus';
}

<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Common;


class WsMessage
{
    const WS_MESSAGE_CMD_EVENT = 'system.event';
    const WS_MESSAGE_CMD_ERROR = 'system.error';
    const EVENT_USER_STATUS = 'setUserStatus';
    const EVENT_GET_MESSAGE = 'getMessage';
    const EVENT_GET_UNREAD_APPLICATION_COUNT = 'getUnreadApplicationCount';
    const EVENT_FRIEND_AGREE_APPLY = 'friendAgreeApply';
    const EVENT_GROUP_AGREE_APPLY = 'groupAgreeApply';
    const EVENT_FRIEND_VIDEO_ROOM = 'friendVideoRoom';
}

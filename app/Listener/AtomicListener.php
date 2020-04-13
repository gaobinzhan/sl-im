<?php declare(strict_types=1);

namespace App\Listener;

use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Log\Helper\CLog;
use Swoft\Server\SwooleEvent;

/**
 * Class AtomicListener - event handler
 *
 * @Listener(SwooleEvent::START)
 */
class AtomicListener implements EventHandlerInterface
{
    /**
     * @param EventInterface $event
     */
    public function handle(EventInterface $event): void
    {
        bean('App\Helper\Atomic');
        CLog::info('Atomic Create Success !');
    }
}

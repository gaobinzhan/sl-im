<?php
/**
 * @author gaobinzhan <gaobinzhan@gmail.com>
 */


namespace App\Helper;

use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Class Atomic
 * @package App\Helper
 * @Bean()
 */
class Atomic
{
    private $atomic;

    public function __construct()
    {
        $this->atomic = new \Swoole\Atomic();
    }

    public function add(int $value)
    {
        return $this->atomic->add($value);
    }

    public function sub(int $value)
    {
        return $this->atomic->sub($value);
    }

    public function get()
    {
        return $this->atomic->get();
    }

    public function set(int $value)
    {
        return $this->atomic->set($value);
    }
}

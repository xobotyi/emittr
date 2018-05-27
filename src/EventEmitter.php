<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use xobotyi\emittr\Interfaces\EventEmitterGlobal;

class EventEmitter implements Interfaces\EventEmitter
{
    private $eventEmitterGlobal;

    private $eventListeners = [];

    private $maxListernersCount = 10;

    public function __construct(?Interfaces\EventEmitterGlobal $emitterGlobal = null) {
        if ($emitterGlobal) {
            $this->setGlobalEmitter($emitterGlobal);
        }
    }

    public function emit(string $eventName, $payload) {
    }

    public function on(string $eventName, $callback) {
    }

    public function once(string $eventName, $callback) {
    }

    public function prependListener(string $eventName, $callback) {
    }

    public function prependOnceListener(string $eventName, $callback) {
    }

    public function off(string $eventName, $callback) {

    }

    public function removeAllListeners(?string $eventName = null) {
    }

    public function getListeners(?string $eventName = null) :array {
        return $eventName ? $this->eventListeners[$eventName] : $this->eventListeners;
    }

    public function getMaxListenersCount() :int {
        return $this->maxListernersCount;
    }

    public function setMaxListenersCount(int $maxListenersCount) :self {
        if ($maxListenersCount < 0) {
            throw new \InvalidArgumentException('Listeners count must be greater or equal 0, got ' . $maxListenersCount);
        }

        $this->maxListernersCount = $maxListenersCount;

        return $this;
    }

    public function getGlobalEmitter() :EventEmitterGlobal {
        return $this->eventEmitterGlobal;
    }

    public function setGlobalEmitter(EventEmitterGlobal $emitterGlobal) :self {
        $this->eventEmitterGlobal = $emitterGlobal;

        return $this;
    }
}
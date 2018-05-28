<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

class EventEmitter implements Interfaces\EventEmitter
{
    public const EVENT_LISTENER_ADDED   = 'listenerAdded';
    public const EVENT_LISTENER_REMOVED = 'listenerRemoved';

    /**
     * @var Interfaces\EventEmitterGlobal;
     */
    private $eventEmitterGlobal;

    private $eventListeners = [];

    private $maxListenersCount = 10;

    public function __construct(?Interfaces\EventEmitterGlobal $emitterGlobal = null) {
        $this->setGlobalEmitter($emitterGlobal ?: EventEmitterGlobal::getInstance());
    }

    public function emit(string $eventName, $payload = null) :self {
        $event = new Event($eventName, $payload, get_called_class(), $this);

        if (empty($this->eventListeners) || $this->eventEmitterGlobal::propagateEvent($event, $this->eventListeners)) {
            $this->eventEmitterGlobal->propagateEventGlobal($event);
        }

        return $this;
    }

    public function on(string $eventName, $callback) :self {
        $this->eventEmitterGlobal::storeCallback($this->eventListeners, $eventName, $callback, $this->maxListenersCount, false, false);

        return $this;
    }

    public function once(string $eventName, $callback) :self {
        $this->eventEmitterGlobal::storeCallback($this->eventListeners, $eventName, $callback, $this->maxListenersCount, true, false);

        return $this;
    }

    public function prependListener(string $eventName, $callback) :self {
        $this->eventEmitterGlobal::storeCallback($this->eventListeners, $eventName, $callback, $this->maxListenersCount, false, true);

        return $this;
    }

    public function prependOnceListener(string $eventName, $callback) :self {
        $this->eventEmitterGlobal::storeCallback($this->eventListeners, $eventName, $callback, $this->maxListenersCount, true, true);

        return $this;
    }

    public function off(string $eventName, $callback) :self {
        if (empty($this->eventListeners[$eventName])) {
            return $this;
        }

        $this->eventListeners[$eventName] = \array_values(\array_filter($this->eventListeners[$eventName], function ($item) use (&$callback) { return $item[1] !== $callback; }));

        if (empty($this->eventListeners[$eventName])) {
            unset($this->eventListeners[$eventName]);
        }

        return $this;
    }

    public function removeAllListeners(?string $eventName = null) :self {
        if ($eventName === null) {
            $this->eventListeners = [];

            return $this;
        }

        if (empty($this->eventListeners[$eventName])) {
            return $this;
        }

        unset($this->eventListeners[$eventName]);

        return $this;
    }

    public function getEventNames() :array {
        return empty($this->eventListeners) ? [] : \array_keys($this->eventListeners);
    }

    public function getListeners(?string $eventName = null) :array {
        return $eventName ? $this->eventListeners[$eventName] ?? [] : $this->eventListeners;
    }

    public function getMaxListenersCount() :int {
        return $this->maxListenersCount;
    }

    public function setMaxListenersCount(int $maxListenersCount) :self {
        if ($maxListenersCount < 0) {
            throw new \InvalidArgumentException('Listeners count must be greater or equal 0, got ' . $maxListenersCount);
        }

        $this->maxListenersCount = $maxListenersCount;

        return $this;
    }

    public function getGlobalEmitter() :Interfaces\EventEmitterGlobal {
        return $this->eventEmitterGlobal;
    }

    public function setGlobalEmitter(Interfaces\EventEmitterGlobal $emitterGlobal) :self {
        $this->eventEmitterGlobal = $emitterGlobal;

        return $this;
    }
}
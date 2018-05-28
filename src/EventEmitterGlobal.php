<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

final class EventEmitterGlobal implements Interfaces\EventEmitterGlobal
{
    /**
     * @var \xobotyi\emittr\EventEmitterGlobal;
     */
    private static $instance;

    private $listeners;

    private $maxListenersCount = 10;

    public static function getInstance() :self {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function propagateEvent(Event $event, array &$eventsListeners) :bool {
        $eventName = $event->getEventName();
        $listeners = &$eventsListeners[$eventName] ?? false;

        if (empty($listeners)) {
            return true;
        }

        $result = true;

        foreach ($listeners as $key => &$listener) {
            if (in_array($eventName, [EventEmitter::EVENT_LISTENER_ADDED, EventEmitter::EVENT_LISTENER_REMOVED,]) &&
                $event->getPayload()['callback'] && $event->getPayload()['callback'] === $listener[1]) {
                continue;
            }

            call_user_func($listener[1], $event);

            if ($listener[0]) {
                unset($listeners[$key]);
            }

            if (!$event->isPropagatable()) {
                $result = false;
                break;
            }
        }

        if (empty($listeners)) {
            unset($listeners);
        }

        return $result;
    }

    public static function isValidCallback($callback) :bool {
        return is_string($callback) || is_callable($callback) || (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && is_string($callback[1]));
    }

    public static function storeCallback(array &$arrayToStore, string $eventName, $callback, int $maxListeners = 10, bool $once = false, bool $prepend = false) :void {
        if (!self::isValidCallback($callback)) {
            throw new Exception\EventEmitter("Event callback has to be a callable or an array of two elements representing classname and method to call");
        }

        if (!empty($arrayToStore[$eventName]) && $maxListeners && count($arrayToStore[$eventName]) >= $maxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $eventName);
        }

        if (empty($arrayToStore[$eventName])) {
            $arrayToStore[$eventName] = [];
        }

        $prepend
            ? array_unshift($arrayToStore[$eventName], [$once, $callback])
            : $arrayToStore[$eventName][] = [$once, $callback];
    }

    public function on(string $className, string $eventName, $callback) :self {
        if (!isset($this->listeners[$className][$eventName])) {
            $this->listeners[$className][$eventName] = [];
        }

        self::storeCallback($this->listeners[$className], $eventName, $callback, $this->maxListenersCount, false, false);

        return $this;
    }

    public function once(string $className, string $eventName, $callback) :self {
        if (!isset($this->listeners[$className][$eventName])) {
            $this->listeners[$className][$eventName] = [];
        }

        self::storeCallback($this->listeners[$className], $eventName, $callback, $this->maxListenersCount, true, false);

        return $this;
    }

    public function prependListener(string $className, string $eventName, $callback) :self {
        if (!isset($this->listeners[$className][$eventName])) {
            $this->listeners[$className][$eventName] = [];
        }

        self::storeCallback($this->listeners[$className], $eventName, $callback, $this->maxListenersCount, false, true);

        return $this;
    }

    public function prependOnceListener(string $className, string $eventName, $callback) :self {
        if (!isset($this->listeners[$className][$eventName])) {
            $this->listeners[$className][$eventName] = [];
        }

        self::storeCallback($this->listeners[$className], $eventName, $callback, $this->maxListenersCount, true, true);

        return $this;
    }

    public function off(string $className, string $eventName, $callback) :self {
        if (empty($this->listeners[$className][$eventName])) {
            return $this;
        }

        $this->listeners[$className][$eventName] = \array_values(\array_filter($this->listeners[$className][$eventName], function ($item) use (&$callback) { return $item[1] !== $callback; }));

        if (empty($this->listeners[$className][$eventName])) {
            unset($this->listeners[$className][$eventName]);
        }

        if (empty($this->listeners[$className])) {
            unset($this->listeners[$className]);
        }

        return $this;
    }

    public function removeAllListeners(?string $className = null, ?string $eventName = null) :self {
        if ($className === null) {
            $this->listeners = [];

            return $this;
        }

        if (empty($this->listeners[$className])) {
            return $this;
        }

        if ($eventName === null) {
            unset($this->listeners[$className]);

            return $this;
        }

        if (empty($this->listeners[$className][$eventName])) {
            return $this;
        }

        unset($this->listeners[$className][$eventName]);

        if (empty($this->listeners[$className])) {
            unset($this->listeners[$className]);
        }

        return $this;
    }

    public function getEventNames(string $className) :array {
        return empty($this->listeners[$className]) ? [] : \array_keys($this->listeners[$className]);
    }

    public function getListeners(?string $className = null, ?string $eventName = null) :array {
        return $className ? $eventName ? $this->listeners[$className][$eventName] ?? [] : $this->listeners[$className] ?? [] : $this->listeners;
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

    public function propagateEventGlobal(Event $event) :bool {
        if (substr($event->getSourceClass(), 0, 15) === 'class@anonymous') {
            return true;
        }

        if (empty($this->listeners[$event->getSourceClass()])) {
            return true;
        }

        return self::propagateEvent($event, $this->listeners[$event->getSourceClass()]);
    }
}
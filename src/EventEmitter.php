<?php
declare(strict_types=1);

/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

/**
 * Class EventEmitter
 *
 * @method self      emit(string $eventName, $payload = null)
 * @method array     getEventNames()
 * @method array     getListeners(?string $eventName = null)
 * @method int       getMaxListeners()
 * @method self      on(string $eventName, callable $callback)
 * @method self      once(string $eventName, callable $callback)
 * @method self      prependListener(string $eventName, callable $callback)
 * @method self      prependOnceListener(string $eventName, callable $callback)
 * @method self      removeAllListeners(?string $eventName = null)
 * @method self      removeListener(string $eventName, callable $callback)
 * @method self      setMaxListeners(int $listenersCount)
 *
 * @method static void      emit(string $eventName, $payload = null)
 * @method static array     getEventNames()
 * @method static array     getListeners(?string $eventName = null)
 * @method static int       getMaxListeners()
 * @method static void      on(string $eventName, callable $callback)
 * @method static void      once(string $eventName, callable $callback)
 * @method static void      prependListener(string $eventName, callable $callback)
 * @method static void      prependOnceListener(string $eventName, callable $callback)
 * @method static void      removeAllListeners(?string $eventName = null)
 * @method static void      removeListener(string $eventName, callable $callback)
 * @method static void      setMaxListeners(int $listenersCount)
 *
 * @package xobotyi\emittr
 */
/**
 * Class EventEmitter
 *
 * @package xobotyi\emittr
 */
class EventEmitter extends EventEmitterStatic
{
    /**
     * @var array
     */
    private $listeners    = [];
    /**
     * @var int
     */
    private $maxListeners = 10;

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (method_exists($this, '_' . $name)) {
            return call_user_func_array([$this, '_' . $name], $arguments);
        }

        throw new \Error('Call to undefined method ' . get_called_class() . '->' . $name . '()');
    }

    /**
     * @param string $eventName
     * @param null   $payload
     *
     * @return \xobotyi\emittr\EventEmitter
     */
    private function _emit(string $eventName, $payload = null) :self {
        $calledClass = get_called_class();
        $event       = new Event($eventName, $payload, $calledClass, $this);

        if (empty($this->listeners) || self::propagateEvent($event, $this->listeners)) {
            if (!(self::$staticListeners[$calledClass] ?? false) || self::propagateEvent($event, self::$staticListeners[$calledClass])) {
                EventEmitterGlobal::propagateClassEvent($event);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    private function _getEventNames() :array {
        return \array_keys($this->listeners);
    }

    /**
     * @param null|string $eventName
     *
     * @return array
     */
    private function _getListeners(?string $eventName = null) :array {
        return $this->listeners[$eventName] ?? [];
    }

    /**
     * @return int
     */
    private function _getMaxListeners() :int {
        return $this->maxListeners;
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @return \xobotyi\emittr\EventEmitter
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private function _on(string $eventName, $callback) :self {
        self::storeCallback($this->listeners, $eventName, $callback, false, false, $this->maxListeners);

        return $this;
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @return \xobotyi\emittr\EventEmitter
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private function _once(string $eventName, $callback) :self {
        self::storeCallback($this->listeners, $eventName, $callback, true, false, $this->maxListeners);

        return $this;
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @return \xobotyi\emittr\EventEmitter
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private function _prependListener(string $eventName, $callback) :self {
        self::storeCallback($this->listeners, $eventName, $callback, false, true, $this->maxListeners);

        return $this;
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @return \xobotyi\emittr\EventEmitter
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private function _prependOnceListener(string $eventName, $callback) :self {
        self::storeCallback($this->listeners, $eventName, $callback, true, true, $this->maxListeners);

        return $this;
    }

    /**
     * @param null|string $eventName
     *
     * @return \xobotyi\emittr\EventEmitter
     */
    private function _removeAllListeners(?string $eventName = null) :self {
        if (empty($this->listeners)) {
            return $this;
        }

        if ($eventName) {
            if (!($this->listeners[$eventName] ?? false)) {
                return $this;
            }

            foreach ($this->listeners[$eventName] as &$callback) {
                $this->_emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $eventName, 'callback' => &$callback[1]]);
            }

            unset($this->listeners[$eventName]);

            return $this;
        }

        foreach ($this->listeners as $eventName => &$listeners) {
            if (!$listeners) {
                continue;
            }

            foreach ($listeners as &$callback) {
                $this->_emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $eventName, 'callback' => &$callback[1]]);
            }
        }

        $this->listeners = [];

        return $this;
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @return \xobotyi\emittr\EventEmitter
     */
    private function _removeListener(string $eventName, $callback) :self {
        if (!($this->listeners[$eventName] ?? false)) {
            return $this;
        }

        $this->listeners[$eventName] = array_filter($this->listeners[$eventName], function ($item) use (&$callback) { return $item[1] !== $callback; });

        if (empty($this->listeners[$eventName])) {
            unset($this->listeners[$eventName]);
        }

        $this->_emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $eventName, 'callback' => &$callback]);

        return $this;
    }

    /**
     * @param int $listenersCount
     *
     * @return \xobotyi\emittr\EventEmitter
     */
    private function _setMaxListeners(int $listenersCount) :self {
        if ($listenersCount <= 0) {
            throw new \InvalidArgumentException('Listeners count must be greater than 0, got ' . $listenersCount);
        }

        $this->maxListeners = $listenersCount;

        return $this;
    }
}
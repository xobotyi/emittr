<?php
declare(strict_types=1);

/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

/**
 * Class EventEmitterStatic
 *
 * @method static void      emit(string $eventName, $payload = null)
 * @method static int       getMaxListeners()
 * @method static void      setMaxListeners(int $listenersCount)
 * @method static array     getEventNames()
 * @method static array     getListeners(?string $eventName = null)
 * @method static void      on(string $eventName, callable $callback)
 * @method static void      once(string $eventName, callable $callback)
 * @method static void      prependListener(string $eventName, callable $callback)
 * @method static void      prependOnceListener(string $eventName, callable $callback)
 * @method static void      removeAllListeners(?string $eventName = null)
 * @method static void      removeListener(string $eventName, callable $callback)
 *
 * @package xobotyi\emittr
 */
class EventEmitterStaticOld
{
    public const EVENT_LISTENER_ADDED   = 'listenerAdded';
    public const EVENT_LISTENER_REMOVED = 'listenerRemoved';

    /**
     * @var array[]
     */
    protected static $staticListeners = [];
    /**
     * @var int[]
     */
    protected static $staticMaxListeners = [];

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments) {
        $calledClass = get_called_class();
        if (method_exists($calledClass, '_' . $name . 'Static')) {
            return forward_static_call_array([$calledClass, '_' . $name . 'Static'], $arguments);
        }

        throw new \Error('Call to undefined method ' . $calledClass . '::' . $name . '()');
    }

    /**
     * @param string $eventName
     * @param mixed  $payload
     */
    private static function _emitStatic(string $eventName, $payload = null) :void {
        $calledClass = get_called_class();

        $event = new Event($eventName, $payload, $calledClass);
        if (!(self::$staticListeners[$calledClass] ?? false) || self::propagateEvent($event, self::$staticListeners[$calledClass])) {
            EventEmitterGlobalOld::propagateClassEvent($event);
        }
    }

    /**
     * @param \xobotyi\emittr\Event $evt
     * @param array                 $eventsListeners
     *
     * @return bool
     */
    protected static function propagateEvent(Event $evt, array &$eventsListeners) :bool {
        $listeners = &$eventsListeners[$evt->getEventName()] ?? false;

        if (!$listeners) {
            return true;
        }

        $res = true;

        foreach ($listeners as $key => &$listener) {
            if (in_array($evt->getEventName(), [self::EVENT_LISTENER_ADDED, self::EVENT_LISTENER_REMOVED,])
                && $listener[1] === $evt->getPayload()['callback']) {
                continue;
            }

            call_user_func($listener[1], $evt);

            if ($listener[0]) {
                unset($listeners[$key]);
            }

            if (!$evt->isPropagatable()) {
                $res = false;
                break;
            }
        }

        if (empty($listeners)) {
            unset($listeners);
        }

        return $res;
    }

    /**
     * @return array
     */
    private static function _getEventNamesStatic() :array {
        return \array_keys(self::$staticListeners[get_called_class()] ?? []);
    }

    /**
     * @param null|string $eventName
     *
     * @return array
     */
    private static function _getListenersStatic(?string $eventName = null) :array {
        return $eventName ? self::$staticListeners[get_called_class()][$eventName] ?? [] : self::$staticListeners[get_called_class()];
    }

    /**
     * @param string   $eventName
     * @param callable $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private static function _onStatic(string $eventName, callable $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $eventName, $callback, false, false, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $eventName, 'callback' => $callback, 'once' => false]);
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private static function _onceStatic(string $eventName, $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $eventName, $callback, true, false, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $eventName, 'callback' => $callback, 'once' => true]);
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private static function _prependListenerStatic(string $eventName, $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $eventName, $callback, false, true, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $eventName, 'callback' => $callback, 'once' => false]);
    }

    /**
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    private static function _prependOnceListenerStatic(string $eventName, $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $eventName, $callback, true, true, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $eventName, 'callback' => $callback, 'once' => true]);
    }

    /**
     * @param null|string $eventName
     */
    private static function _removeAllListenersStatic(?string $eventName = null) :void {
        $calledClass = get_called_class();

        if (!(self::$staticListeners[$calledClass] ?? false)) {
            return;
        }

        if ($eventName) {
            if (!(self::$staticListeners[$calledClass][$eventName] ?? false)) {
                return;
            }

            foreach (self::$staticListeners[$calledClass][$eventName] as &$callback) {
                self::emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $eventName, 'callback' => &$callback[1]]);
            }

            unset(self::$staticListeners[$calledClass][$eventName]);
            self::$staticListeners[$calledClass] = array_filter(self::$staticListeners[$calledClass], function ($item) { return !empty($item); });

            return;
        }

        foreach (self::$staticListeners[$calledClass] as $eventName => &$listeners) {
            if (!$listeners) {
                continue;
            }

            foreach ($listeners as &$callback) {
                self::emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $eventName, 'callback' => &$callback[1]]);
            }
        }

        self::$staticListeners[$calledClass] = [];
    }

    /**
     * @param string   $eventName
     * @param callable $callback
     */
    private static function _removeListenerStatic(string $eventName, callable $callback) :void {
        $calledClass = get_called_class();
        if (!(self::$staticListeners[$calledClass][$eventName] ?? false)) {
            return;
        }

        self::$staticListeners[$calledClass][$eventName] = array_filter(self::$staticListeners[$calledClass][$eventName],
            function ($item) use (&$callback) { return $item[1] !== $callback; });

        if (empty(self::$staticListeners[$calledClass][$eventName])) {
            unset(self::$staticListeners[$calledClass][$eventName]);
            self::$staticListeners[$calledClass] = array_filter(self::$staticListeners[$calledClass], function ($item) { return !empty($item); });
        }

        self::emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $eventName, 'callback' => &$callback]);
    }

    /**
     * @param int $listenersCount
     */
    private static function _setMaxListenersStatic(int $listenersCount) :void {
        if ($listenersCount < 0) {
            throw new \InvalidArgumentException('Listeners count must be greater or equal 0, got ' . $listenersCount);
        }

        self::$staticMaxListeners[get_called_class()] = $listenersCount;
    }

    /**
     * @return int
     */
    private static function _getMaxListenersStatic() :int {
        return self::$staticMaxListeners[get_called_class()] ?? 10;
    }

    /**
     * @param $callback
     *
     * @return bool
     */
    protected static function isValidCallback($callback) :bool {
        return is_string($callback) || is_callable($callback) || (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && is_string($callback[1]));
    }

    /**
     * @param array    $arrayToStore
     * @param string   $eventName
     * @param          $callback
     * @param bool     $once
     * @param bool     $prepend
     * @param int|null $maxListeners
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    protected static function storeCallback(array &$arrayToStore, string $eventName, &$callback, bool $once = false, bool $prepend = false, ?int $maxListeners = null) :void {
        if (!self::isValidCallback($callback)) {
            throw new Exception\EventEmitter("Event callback has to be a callable or an array of two elements representing classname and method to call");
        }

        $maxListeners = $maxListeners === null ? self::_getMaxListenersStatic() : $maxListeners;

        if (($arrayToStore[$eventName] ?? false) && $maxListeners && count($arrayToStore[$eventName]) === $maxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $eventName);
        }

        if (!isset($arrayToStore[$eventName])) {
            $arrayToStore[$eventName] = [];
        }

        $prepend
            ? array_unshift($arrayToStore[$eventName], [$once, $callback])
            : $arrayToStore[$eventName][] = [$once, $callback];
    }
}
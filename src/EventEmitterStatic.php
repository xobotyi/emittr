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
 * @method static void      emit(string $evtName, $payload = null)
 * @method static int       getMaxListeners()
 * @method static void      setMaxListeners(int $listenersCount)
 * @method static array     getEventNames()
 * @method static array     getListeners(?string $eventName = null)
 * @method static void      on(string $evtName, callable $callback)
 * @method static void      once(string $evtName, callable $callback)
 * @method static void      prependListener(string $evtName, callable $callback)
 * @method static void      prependOnceListener(string $evtName, callable $callback)
 * @method static void      removeAllListeners(?string $evtName = null)
 * @method static void      removeListener(string $evtName, callable $callback)
 *
 * @package xobotyi\emittr
 */
class EventEmitterStatic
{
    public const EVENT_LISTENER_ADDED   = 'listenerAdded';
    public const EVENT_LISTENER_REMOVED = 'listenerRemoved';

    protected static $staticListeners    = [];
    protected static $staticMaxListeners = [];

    public static function __callStatic($name, $arguments) {
        $calledClass = get_called_class();
        if (method_exists($calledClass, '_' . $name . 'Static')) {
            return forward_static_call_array([$calledClass, '_' . $name . 'Static'], $arguments);
        }

        throw new \Error('Call to undefined method ' . $calledClass . '::' . $name . '()');
    }

    private static function _emitStatic(string $evtName, $payload = null) :void {
        $calledClass = get_called_class();

        $event = new Event($evtName, $payload, $calledClass);
        if (!(self::$staticListeners[$calledClass] ?? false) || self::propagateEvent($event, self::$staticListeners[$calledClass])) {
            EventEmitterGlobal::propagateClassEvent($event);
        }
    }

    protected static function propagateEvent(Event $evt, array &$listeners) :bool {
        if (!($listeners = &$listeners[$evt->getEventName()] ?? false)) {
            return true;
        }

        $res = true;

        foreach ($listeners as $key => &$listener) {
            if (($evt->getEventName() === self::EVENT_LISTENER_ADDED || $evt->getEventName() === self::EVENT_LISTENER_REMOVED) &&
                $listener[1] === $evt->getPayload()['callback']) {
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

        if (!count($listeners)) {
            unset($listeners);
        }

        return $res;
    }

    private static function _getEventNamesStatic() :array {
        return \array_keys(self::$staticListeners[get_called_class()] ?? []);
    }

    private static function _getListenersStatic(?string $eventName = null) :array {
        return $eventName ? self::$staticListeners[get_called_class()][$eventName] ?? [] : self::$staticListeners[get_called_class()];
    }

    private static function _onStatic(string $evtName, callable $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $evtName, $callback, false, false, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => false]);
    }

    private static function _onceStatic(string $evtName, $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $evtName, $callback, true, false, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => true]);
    }

    private static function _prependListenerStatic(string $evtName, $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $evtName, $callback, false, true, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => false]);
    }

    private static function _prependOnceListenerStatic(string $evtName, $callback) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            self::$staticListeners[$calledClass] = [];
        }

        self::storeCallback(self::$staticListeners[$calledClass], $evtName, $callback, true, true, self::_getMaxListenersStatic());

        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => true]);
    }

    private static function _removeAllListenersStatic(?string $evtName = null) :void {
        $calledClass = get_called_class();

        if (!isset(self::$staticListeners[$calledClass])) {
            return;
        }

        if (!$evtName) {
            foreach (self::$staticListeners as $eventname => &$listeners) {
                foreach ($listeners as &$callback) {
                    self::emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $evtName, 'callback' => &$callback[1]]);
                }
            }

            self::$staticListeners[$calledClass] = [];

            return;
        }

        if (self::$staticListeners[$calledClass][$evtName] ?? false) {
            foreach (self::$staticListeners[$calledClass][$evtName] as &$callback) {
                self::emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $evtName, 'callback' => &$callback[1]]);
            }

            unset(self::$staticListeners[$calledClass][$evtName]);
        }
    }

    private static function _removeListenerStatic(string $evtName, callable $callback) :void {
        $calledClass = get_called_class();
        if (!(self::$staticListeners[$calledClass][$evtName] ?? false)) {
            return;
        }

        self::$staticListeners[$calledClass][$evtName] = array_filter(self::$staticListeners[$calledClass][$evtName], function ($item) use (&$callback) { return $item[1] !== $callback; });

        if (empty(self::$staticListeners[$calledClass][$evtName])) {
            unset(self::$staticListeners[$calledClass][$evtName]);
        }

        self::emit(self::EVENT_LISTENER_REMOVED, ['eventName' => $evtName, 'callback' => &$callback]);
    }

    private static function _setMaxListenersStatic(int $listenersCount) :void {
        if ($listenersCount < 0) {
            throw new \InvalidArgumentException('Listeners count must be greater or equal 0, got ' . $listenersCount);
        }

        self::$staticMaxListeners[get_called_class()] = $listenersCount;
    }

    private static function _getMaxListenersStatic() :int {
        return self::$staticMaxListeners[get_called_class()] ?? 10;
    }

    protected static function isValidCallback($callback) :bool {
        return is_callable($callback) || (is_array($callback) && count($callback) === 2 && is_string($callback[0]) && is_string($callback[1]));
    }

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
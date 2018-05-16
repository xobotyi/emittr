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
        if (self::propagateEvent($event, self::$staticListeners[$calledClass])) {
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

        if ((self::$staticListeners[$calledClass][$evtName] ?? false) && self::_getMaxListenersStatic() && count(self::$staticListeners[$calledClass][$evtName]) === self::_getMaxListenersStatic()) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . $calledClass);
        }

        self::$staticListeners[$calledClass][$evtName][] = [false, &$callback,];
        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => false]);
    }

    private static function _onceStatic(string $evtName, callable $callback) :void {
        $calledClass = get_called_class();
        if ((self::$staticListeners[$calledClass][$evtName] ?? false) && self::_getMaxListenersStatic() && count(self::$staticListeners[$calledClass][$evtName]) === self::_getMaxListenersStatic()) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . $calledClass);
        }

        self::$staticListeners[$calledClass][$evtName][] = [true, &$callback,];
        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => true]);
    }

    private static function _prependListenerStatic(string $evtName, callable $callback) :void {
        $calledClass = get_called_class();
        if ((self::$staticListeners[$calledClass][$evtName] ?? false) && self::_getMaxListenersStatic() && count(self::$staticListeners[$calledClass][$evtName]) === self::_getMaxListenersStatic()) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . $calledClass);
        }

        if (!isset(self::$staticListeners[$calledClass][$evtName])) {
            self::$staticListeners[$calledClass][$evtName] = [];
        }

        array_unshift(self::$staticListeners[$calledClass][$evtName], [false, &$callback,]);
        self::emit(self::EVENT_LISTENER_ADDED, ['eventName' => $evtName, 'callback' => $callback, 'once' => false]);
    }

    private static function _prependOnceListenerStatic(string $evtName, callable $callback) :void {
        $calledClass = get_called_class();
        if ((self::$staticListeners[$calledClass][$evtName] ?? false) && self::_getMaxListenersStatic() && count(self::$staticListeners[$calledClass][$evtName]) === self::_getMaxListenersStatic()) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . $calledClass);
        }

        if (!isset(self::$staticListeners[$calledClass][$evtName])) {
            self::$staticListeners[$calledClass][$evtName] = [];
        }

        array_unshift(self::$staticListeners[$calledClass][$evtName], [true, &$callback,]);
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
}
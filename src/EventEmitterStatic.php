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
 * @package xobotyi\emittr
 */
class EventEmitterStatic
{
    public const EVENT_LISTENER_ADDED   = 'listenerAdded';
    public const EVENT_LISTENER_REMOVED = 'listenerRemoved';

    protected static $staticListeners    = [];
    private static   $staticMaxListeners = 10;

    public static function __callStatic($name, $arguments) {
        if (method_exists(self::class, '_' . $name . 'Static')) {
            return forward_static_call_array([self::class, '_' . $name . 'Static'], $arguments);
        }

        throw new \Error('Call to undefined method ' . get_called_class() . '::' . $name . '()');
    }

    private static function _emitStatic(string $evtName, $payload = null) :void {
        self::propagateEvent(new Event($evtName, $payload, get_called_class()), self::$staticListeners);
    }

    protected static function propagateEvent(Event $evt, array &$listeners) :bool {
        if (!isset($listeners[$evt->getEventName()])) {
            return true;
        }

        $res = true;

        foreach ($listeners[$evt->getEventName()] as $key => &$listener) {
            call_user_func($listener[1], $evt);

            if ($listener[0]) {
                unset($listeners[$evt->getEventName()][$key]);
            }

            if (!$evt->isPropagatable()) {
                $res = false;
                break;
            }
        }

        if (!count($listeners[$evt->getEventName()])) {
            unset($listeners[$evt->getEventName()]);
        }

        return $res;
    }

    private static function _getEventNamesStatic() :array {
        return \array_keys(self::$staticListeners);
    }

    private static function _getListenersStatic(?string $eventName = null) :array {
        return self::$staticListeners[$eventName] ?? [];
    }

    private static function _getMaxListenersStatic() :int {
        return self::$staticMaxListeners;
    }

    private static function _onStatic(string $evtName, callable $cb) :void {
        if ((self::$staticListeners[$evtName] ?? false) && self::$staticMaxListeners && count(self::$staticListeners[$evtName]) === self::$staticMaxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        self::$staticListeners[$evtName][] = [false, &$cb,];
    }

    private static function _onceStatic(string $evtName, callable $cb) :void {
        if ((self::$staticListeners[$evtName] ?? false) && self::$staticMaxListeners && count(self::$staticListeners[$evtName]) === self::$staticMaxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        self::$staticListeners[$evtName][] = [true, &$cb,];
    }

    private static function _prependListenerStatic(string $evtName, callable $cb) :void {
        if ((self::$staticListeners[$evtName] ?? false) && self::$staticMaxListeners && count(self::$staticListeners[$evtName]) === self::$staticMaxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        array_unshift(self::$staticListeners[$evtName], [false, &$cb,]);
    }

    private static function _prependOnceListenerStatic(string $evtName, callable $cb) :void {
        if ((self::$staticListeners[$evtName] ?? false) && self::$staticMaxListeners && count(self::$staticListeners[$evtName]) === self::$staticMaxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        array_unshift(self::$staticListeners[$evtName], [true, &$cb,]);
    }

    private static function _removeAllListenersStatic(?string $evtName) :void {
        if ($evtName) {
            unset(self::$staticListeners[$evtName]);
        }

        self::$staticListeners = [];
    }

    private static function _removeListenerStatic(string $evtName, callable $cb) :void {
        if (!(self::$staticListeners[$evtName] ?? false)) {
            return;
        }

        self::$staticListeners[$evtName] = array_filter(self::$staticListeners[$evtName], function ($item) use (&$cb) { return $item[1] !== $cb; });

        if (empty(self::$staticListeners[$evtName])) {
            unset(self::$staticListeners[$evtName]);
        }
    }

    private static function _setMaxListenersStatic(int $listenersCount) :void {
        if ($listenersCount < 0) {
            throw new \InvalidArgumentException('Listeners count must be greater or equal 0, got ' . $listenersCount);
        }

        self::$staticMaxListeners = $listenersCount;
    }
}
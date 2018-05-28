<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr\Traits;


use xobotyi\emittr\Event;
use xobotyi\emittr\Interfaces\EventEmitter;
use xobotyi\emittr\Interfaces\EventEmitterGlobal;

trait EventEmitterStatic
{
    /**
     * @var EventEmitter;
     */
    private static $eventEmitter;

    public static function getEventEmitter() :EventEmitter {
        return self::$eventEmitter ?: self::setEventEmitter(new \xobotyi\emittr\EventEmitter());
    }

    public static function setEventEmitter(EventEmitter $eventEmitter) :EventEmitter {
        return self::$eventEmitter = $eventEmitter;
    }

    public static function emit(string $eventName, $payload = null) :void {
        self::getEventEmitter()->emit(new Event($eventName, $payload, get_called_class(), null));
    }

    public static function on(string $eventName, $callback) :void {
        self::getEventEmitter()->on($eventName, $callback);
    }

    public static function once(string $eventName, $callback) :void {
        self::getEventEmitter()->once($eventName, $callback);
    }

    public static function prependListener(string $eventName, $callback) :void {
        self::getEventEmitter()->prependListener($eventName, $callback);
    }

    public static function prependOnceListener(string $eventName, $callback) :void {
        self::getEventEmitter()->prependOnceListener($eventName, $callback);
    }

    public static function off(string $eventName, $callback) {
        self::getEventEmitter()->off($eventName, $callback);
    }

    public static function removeAllListeners(?string $eventName = null) :void {
        self::getEventEmitter()->removeAllListeners($eventName);
    }

    public static function getListeners(?string $eventName = null) :array {
        return self::getEventEmitter()->getListeners($eventName);
    }

    public static function getMaxListenersCount() :int {
        return self::getEventEmitter()->getMaxListenersCount();
    }

    public static function setMaxListenersCount(int $maxListenersCount) :void {
        self::getEventEmitter()->setMaxListenersCount($maxListenersCount);
    }

    public static function getGlobalEmitter() :EventEmitterGlobal {
        return self::getEventEmitter()->getGlobalEmitter();
    }

    public static function setGlobalEmitter(EventEmitterGlobal $emitterGlobal) :void {
        self::getEventEmitter()->setGlobalEmitter($emitterGlobal);
    }
}
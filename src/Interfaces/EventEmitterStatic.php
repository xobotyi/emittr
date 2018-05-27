<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr\Interfaces;


interface EventEmitterStatic
{
    public static function emit(string $eventName, $payload = null);

    public static function on(string $eventName, $callback);

    public static function once(string $eventName, $callback);

    public static function off(string $eventName, $callback);

    public static function prependListener(string $eventName, $callback);

    public static function prependOnceListener(string $eventName, $callback);

    public static function removeAllListeners(?string $eventName = null);

    public static function getListeners(?string $eventName = null) :array;

    public static function getMaxListenersCount() :int;

    public static function setMaxListenersCount(int $maxListenersCount);

    public static function getGlobalEmitter() :EventEmitterGlobal;

    public static function setGlobalEmitter(EventEmitterGlobal $emitterGlobal);
}
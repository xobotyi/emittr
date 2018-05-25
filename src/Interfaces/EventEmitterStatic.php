<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr\Interfaces;


interface EventEmitterStatic
{
    public static function on(string $eventName, $callback);

    public static function once(string $eventName, $callback);

    public static function off(string $eventName, $callback);

    public static function prependListener(string $eventName, $callback);

    public static function prependOnceListener(string $eventName, $callback);

    public static function removeAllListers(?string $eventName = null);

    public static function getListers(?string $eventName = null);

    public static function getMaxListers() :int;

    public static function setMaxListers(int $maxListernersCount);

    public static function getGlobalEmitter() :EventEmitterGlobal;

    public static function setGlobalEmitter(EventEmitterGlobal $emitterGlobal);
}
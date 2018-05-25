<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr\Interfaces;

use xobotyi\emittr\Event;

interface EventEmitterGlobal
{
    public function on(string $className, string $eventName, $callback);

    public function once(string $className, string $eventName, $callback);

    public function off(string $className, string $eventName, $callback);

    public function prependListener(string $className, string $eventName, $callback);

    public function prependOnceListener(string $className, string $eventName, $callback);

    public function removeAllListers(string $className, ?string $eventName = null);

    public function getListers(?string $className = null, ?string $eventName = null) :array;

    public function getMaxListersCount() :int;

    public function setMaxListersCount(int $maxListernersCount);

    public function propagateEventGlobal(Event $event, array &$eventsListeners) :bool;

    public static function getInstance();

    public static function propagateEvent(Event $event, array &$eventsListeners) :bool;

    public static function isValidCallback($callback) :bool;

    public static function storeCallback(array &$arrayToStore, string $eventName, $callback, int $maxListeners = 10, bool $once = false, bool $prepend = false) :void;
}
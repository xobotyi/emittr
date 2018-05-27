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

    public function removeAllListeners(string $className, ?string $eventName = null);

    public function getListeners(?string $className = null, ?string $eventName = null) :array;

    public function getMaxListenersCount() :int;

    public function setMaxListenersCount(int $maxListenersCount);

    public function propagateEventGlobal(Event $event, array &$eventsListeners) :bool;

    public static function getInstance();

    public static function propagateEvent(Event $event, array &$eventsListeners) :bool;

    public static function isValidCallback($callback) :bool;

    public static function storeCallback(array &$arrayToStore, string $eventName, $callback, int $maxListeners = 10, bool $once = false, bool $prepend = false) :void;
}
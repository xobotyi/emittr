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
    /**
     * Return the instance of global emitter
     *
     * @return mixed
     */
    public static function getInstance();

    /**
     * Propagate event through the given listeners array
     *
     * @param \xobotyi\emittr\Event $event
     * @param array[]               $eventsListeners
     *
     * @return bool
     */
    public static function propagateEvent(Event $event, array &$eventsListeners) :bool;

    /**
     * Check if given variable is possibly valid callback
     *
     * @param $callback
     *
     * @return bool
     */
    public static function isValidCallback($callback) :bool;

    /**
     * Store the callback to given listeners array
     *
     * @param array                 $arrayToStore
     * @param string                $eventName
     * @param callable|array|string $callback
     * @param int                   $maxListeners
     * @param bool                  $once
     * @param bool                  $prepend
     */
    public static function storeCallback(array &$arrayToStore, string $eventName, $callback, int $maxListeners = 10, bool $once = false, bool $prepend = false) :void;

    /**
     * Assign listeners from an array
     *
     * @param array $listeners
     *
     * @return mixed
     */
    public function loadListeners(array $listeners);

    /**
     * Append the event listen
     *
     * @param string                $className
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function on(string $className, string $eventName, $callback);

    /**
     * Append the event listener that will fire only once
     *
     * @param string                $className
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function once(string $className, string $eventName, $callback);

    /**
     * Remove the given event listener
     *
     * @param string                $className
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function off(string $className, string $eventName, $callback);

    /**
     * Prepend the event listener
     *
     * @param string                $className
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function prependListener(string $className, string $eventName, $callback);

    /**
     * Prepend the event listener that will fire only once
     *
     * @param string                $className
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function prependOnceListener(string $className, string $eventName, $callback);

    /**
     * Remove all the listeners of certain event or the whole class event listeners
     *
     * @param string      $className
     * @param null|string $eventName
     *
     * @return mixed
     */
    public function removeAllListeners(string $className, ?string $eventName = null);

    /**
     * Get all event names that has listeners for certain class
     *
     * @param string $className
     *
     * @return array
     */
    public function getEventNames(string $className) :array;

    /**
     * Get all listeners of certain event, class or all global listeners
     *
     * @param null|string $className
     * @param null|string $eventName
     *
     * @return array
     */
    public function getListeners(?string $className = null, ?string $eventName = null) :array;

    /**
     * Get max listeners count
     *
     * @return int
     */
    public function getMaxListenersCount() :int;

    /**
     * Set max listeners count
     *
     * @param int $maxListenersCount
     *
     * @return mixed
     */
    public function setMaxListenersCount(int $maxListenersCount);

    /**
     * Propagate event through the global listeners
     *
     * @param \xobotyi\emittr\Event $event
     *
     * @return bool
     */
    public function propagateEventGlobal(Event $event) :bool;
}
<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr\Interfaces;


interface EventEmitter
{
    public function __construct(?EventEmitterGlobal $emitterGlobal = null);

    /**
     * Emit the new event.
     *
     * Once the event was emitted, it will propagate through all the listeners assigned on the certain EventEmitter,
     * then it will propagate through listeners assigned for that class in GlobalEventEmitter.
     *
     * Each listener has the ability to stop event propagation.
     *
     * @param string|\xobotyi\emittr\Event $event
     * @param mixed                        $payload
     *
     * @return mixed
     */
    public function emit($event, $payload = null);

    /**
     * Add the event listener that will fire each time event emitted
     *
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function on(string $eventName, $callback);

    /**
     * Add the event listener that will fire only once
     *
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function once(string $eventName, $callback);

    /**
     * Prepend the event listener
     *
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function prependListener(string $eventName, $callback);

    /**
     * Prepend the event listener that will fire only once
     *
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function prependOnceListener(string $eventName, $callback);

    /**
     * Remove the event listener
     *
     * @param string                $eventName
     * @param callable|array|string $callback
     *
     * @return mixed
     */
    public function off(string $eventName, $callback);

    /**
     * Remove all listeners of certain event or all listeners of emitter
     *
     * @param null|string $eventName
     *
     * @return mixed
     */
    public function removeAllListeners(?string $eventName = null);

    /**
     * Get all event names that has listeners
     *
     * @return array
     */
    public function getEventNames() :array;

    /**
     * Get all listeners of certain event or all listeners of emitter
     *
     * @param null|string $eventName
     *
     * @return array
     */
    public function getListeners(?string $eventName = null) :array;

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
     * Get the to global event emitter
     *
     * @return \xobotyi\emittr\Interfaces\EventEmitterGlobal
     */
    public function getGlobalEmitter() :EventEmitterGlobal;

    /**
     * Set the global event emitter
     *
     * @param \xobotyi\emittr\Interfaces\EventEmitterGlobal $emitterGlobal
     *
     * @return mixed
     */
    public function setGlobalEmitter(EventEmitterGlobal $emitterGlobal);
}
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

    public function emit(string $eventName, $payload);

    public function on(string $eventName, $callback);

    public function once(string $eventName, $callback);

    public function prependListener(string $eventName, $callback);

    public function prependOnceListener(string $eventName, $callback);

    public function off(string $eventName, $callback);

    public function removeAllListers(?string $eventName = null);

    public function getListers(?string $eventName = null) :array;

    public function getMaxListersCount() :int;

    public function setMaxListersCount(int $maxListernersCount);

    public function getGlobalEmitter() :EventEmitterGlobal;

    public function setGlobalEmitter(EventEmitterGlobal $emitterGlobal);
}
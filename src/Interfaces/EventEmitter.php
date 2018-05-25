<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr\Interfaces;


interface EventEmitter
{
    public function on(string $eventName, $callback);

    public function once(string $eventName, $callback);

    public function off(string $eventName, $callback);

    public function prependListener(string $eventName, $callback);

    public function prependOnceListener(string $eventName, $callback);

    public function removeAllListers(?string $eventName = null);

    public function getListers(?string $eventName = null);

    public function getMaxListers() :int;

    public function setMaxListers(int $maxListernersCount);

    public function getGlobalEmitter() :EventEmitterGlobal;

    public function setGlobalEmitter(EventEmitterGlobal $emitterGlobal);
}
<?php
declare(strict_types=1);

/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

/**
 * Class EventEmitter
 *
 * @method static void      emit(string $evtName, $payload = null)
 * @method static array     getEventNames()
 * @method static array     getListeners(?string $eventName = null)
 * @method static int       getMaxListeners()
 * @method static void      on(string $evtName, callable $cb)
 * @method static void      once(string $evtName, callable $cb)
 * @method static void      prependListener(string $evtName, callable $cb)
 * @method static void      prependOnceListener(string $evtName, callable $cb)
 * @method static void      removeAllListeners(?string $evtName)
 * @method static void      removeListener(string $evtName, callable $cb)
 * @method static void      setMaxListeners(int $listenersCount)
 *
 * // * @method self      emit(string $evtName, $payload = null)
 * // * @method array     getEventNames()
 * // * @method array     getListeners(?string $eventName = null)
 * // * @method int       getMaxListeners()
 * // * @method self      on(string $evtName, callable $cb)
 * // * @method self      once(string $evtName, callable $cb)
 * // * @method self      prependListener(string $evtName, callable $cb)
 * // * @method self      prependOnceListener(string $evtName, callable $cb)
 * // * @method self      removeAllListeners(?string $evtName)
 * // * @method self      removeListener(string $evtName, callable $cb)
 * // * @method self      setMaxListeners(int $listenersCount)
 *
 * @package xobotyi\emittr
 */
class EventEmitter extends EventEmitterStatic
{
    private $listeners    = [];
    private $maxListeners = 10;

    public function __call($name, $arguments) {
        if (method_exists($this, '_' . $name)) {
            return call_user_func_array([$this, '_' . $name], $arguments);
        }

        throw new \Error('Call to undefined method ' . get_called_class() . '->' . $name . '()');
    }

    private function _emit(string $evtName, $payload = null) :self {
        $event = new Event($evtName, $payload, get_called_class(), $this);

        if (self::propagateEvent($event, $this->listeners)) {
            if(self::propagateEvent($event, self::$staticListeners)){
                EventEmitterGlobal::propagateClassEvent($event);
            }
        }

        return $this;
    }

    private function _getEventNames() :array {
        return \array_keys($this->listeners);
    }

    private function _getListeners(?string $eventName = null) :array {
        return $this->listeners[$eventName] ?? [];
    }

    private function _getMaxListeners() :int {
        return $this->maxListeners;
    }

    private function _on(string $evtName, callable $cb) :self {
        if (($this->listeners[$evtName] ?? false) && $this->maxListeners && count($this->listeners[$evtName]) === $this->maxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        $this->listeners[$evtName][] = [false, &$cb,];

        return $this;
    }

    private function _once(string $evtName, callable $cb) :self {
        if (($this->listeners[$evtName] ?? false) && $this->maxListeners && count($this->listeners[$evtName]) === $this->maxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        $this->listeners[$evtName][] = [true, &$cb,];

        return $this;
    }

    private function _prependListener(string $evtName, callable $cb) :self {
        if (($this->listeners[$evtName] ?? false) && $this->maxListeners && count($this->listeners[$evtName]) === $this->maxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        array_unshift($this->listeners[$evtName], [false, &$cb,]);

        return $this;
    }

    private function _prependOnceListener(string $evtName, callable $cb) :self {
        if (($this->listeners[$evtName] ?? false) && $this->maxListeners && count($this->listeners[$evtName]) === $this->maxListeners) {
            throw new Exception\EventEmitter("Maximum amount of listeners reached for event " . $evtName . " of " . get_called_class());
        }

        array_unshift($this->listeners[$evtName], [true, &$cb,]);

        return $this;
    }

    private function _removeAllListeners(?string $evtName) :self {
        if ($evtName) {
            unset($this->listeners[$evtName]);
        }

        $this->listeners = [];

        return $this;
    }

    private function _removeListener(string $evtName, callable $cb) :self {
        if (!($this->listeners[$evtName] ?? false)) {
            return $this;
        }

        $this->listeners[$evtName] = array_filter($this->listeners[$evtName], function ($item) use (&$cb) { return $item[1] !== $cb; });

        if (empty($this->listeners[$evtName])) {
            unset($this->listeners[$evtName]);
        }

        return $this;
    }

    private function _setMaxListeners(int $listenersCount) :self {
        if ($listenersCount <= 0) {
            throw new \InvalidArgumentException('Listeners count must be greater than 0, got ' . $listenersCount);
        }

        $this->maxListeners = $listenersCount;

        return $this;
    }
}
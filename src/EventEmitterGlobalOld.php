<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

/**
 * Class EventEmitterGlobalOld
 *
 * @package xobotyi\emittr
 */
final class EventEmitterGlobalOld extends EventEmitterStaticOld
{
    /**
     * @var array[]
     */
    private static $classesListeners = [];

    /**
     * @param array $classesListeners
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    public static function loadClassesEventListeners(array $classesListeners) :void {
        foreach ($classesListeners as $className => &$listeners) {
            if (!isset(self::$classesListeners[$className])) {
                self::$classesListeners[$className] = [];
            }

            foreach ($listeners as $eventName => &$callbacks) {
                if (self::isValidCallback($callbacks)) {
                    self::storeCallback(self::$classesListeners[$className], $eventName, $callbacks);

                    continue;
                }
                else if (is_array($callbacks)) {
                    foreach ($callbacks as &$callback) {
                        self::storeCallback(self::$classesListeners[$className], $eventName, $callback);
                    }

                    continue;
                }

                throw new Exception\EventEmitter("Event callback has to be a callable or an array of two elements representing classname and method to call or array of them");
            }
        }
    }

    /**
     * @param null|string $className
     * @param null|string $eventName
     *
     * @return array
     */
    public static function getListeners(?string $className = null, ?string $eventName = null) :array {
        return $className ? $eventName ? self::$classesListeners[$className][$eventName] ?? [] : self::$classesListeners[$className] ?? [] : self::$classesListeners;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed|void
     */
    public static function __callStatic($name, $arguments) {
        throw new \Error('Call to undefined method ' . get_called_class() . '::' . $name . '()');
    }

    /**
     * @param string $className
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    public static function on(string $className, string $eventName, $callback) :void {
        if (!(self::$classesListeners[$className] ?? false)) {
            self::$classesListeners[$className] = [];
        }

        self::storeCallback(self::$classesListeners[$className], $eventName, $callback, false, false, self::$staticMaxListeners[get_called_class()] ?? 10);
    }

    /**
     * @param string $className
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    public static function once(string $className, string $eventName, $callback) :void {
        if (!(self::$classesListeners[$className] ?? false)) {
            self::$classesListeners[$className] = [];
        }

        self::storeCallback(self::$classesListeners[$className], $eventName, $callback, true, false, self::$staticMaxListeners[get_called_class()] ?? 10);
    }

    /**
     * @param string $className
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    public static function prependListener(string $className, string $eventName, $callback) :void {
        if (!(self::$classesListeners[$className] ?? false)) {
            self::$classesListeners[$className] = [];
        }

        self::storeCallback(self::$classesListeners[$className], $eventName, $callback, false, true, self::$staticMaxListeners[get_called_class()] ?? 10);
    }

    /**
     * @param string $className
     * @param string $eventName
     * @param        $callback
     *
     * @throws \xobotyi\emittr\Exception\EventEmitter
     */
    public static function prependOnceListener(string $className, string $eventName, $callback) :void {
        if (!(self::$classesListeners[$className] ?? false)) {
            self::$classesListeners[$className] = [];
        }

        self::storeCallback(self::$classesListeners[$className], $eventName, $callback, true, true, self::$staticMaxListeners[get_called_class()] ?? 10);
    }

    /**
     * @param string $className
     * @param string $eventName
     * @param        $callback
     */
    public static function removeListener(string $className, string $eventName, $callback) :void {
        if (!(self::$classesListeners[$className][$eventName] ?? false)) {
            return;
        }

        self::$classesListeners[$className][$eventName] = array_filter(self::$classesListeners[$className][$eventName],
            function ($item) use (&$callback) { return $item[1] !== $callback; });

        if (empty(self::$classesListeners[$className][$eventName])) {
            unset(self::$classesListeners[$className][$eventName]);
            self::$classesListeners[$className] = array_filter(self::$classesListeners[$className], function ($item) { return !empty($item); });
        }
    }

    /**
     * @param string      $className
     * @param string|null $eventName
     */
    public static function removeAllListeners(string $className, ?string $eventName = null) :void {
        if (!(self::$classesListeners[$className] ?? false)) {
            return;
        }

        if ($eventName) {
            if (!(self::$classesListeners[$className][$eventName] ?? false)) {
                return;
            }

            unset(self::$classesListeners[$className][$eventName]);
            self::$classesListeners[$className] = array_filter(self::$classesListeners[$className], function ($item) { return !empty($item); });

            return;
        }

        self::$classesListeners[$className] = [];
    }

    /**
     * @return int
     */
    public static function getMaxListeners() :int {
        return self::$staticMaxListeners[get_called_class()] ?? 10;
    }

    /**
     * @param int $listenersCount
     */
    public static function setMaxListeners(int $listenersCount) :void {
        if ($listenersCount < 0) {
            throw new \InvalidArgumentException('Listeners count must be greater or equal 0, got ' . $listenersCount);
        }

        self::$staticMaxListeners[get_called_class()] = $listenersCount;
    }

    /**
     * @param \xobotyi\emittr\Event $evt
     *
     * @return bool
     */
    public static function propagateClassEvent(Event $evt) {
        if (substr($evt->getSourceClass(), 0, 15) === 'class@anonymous') {
            return true;
        }

        $listeners = &self::$classesListeners[$evt->getSourceClass()][$evt->getEventName()] ?? false;

        if (!$listeners) {
            return true;
        }

        $res = true;

        foreach ($listeners as $key => &$listener) {
            call_user_func($listener[1], $evt);

            if ($listener[0]) {
                unset($listeners[$key]);
            }

            if (!$evt->isPropagatable()) {
                $res = false;
                break;
            }
        }

        if (empty($listeners)) {
            unset($listeners);
        }

        return $res;
    }
}
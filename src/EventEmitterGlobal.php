<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


final class EventEmitterGlobal extends EventEmitterStatic
{
    /**
     * @var array[]
     */
    private static $classesListeners = [];

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

    public static function getListeners(?string $className = null, ?string $eventName = null) :array {
        return $className ? $eventName ? self::$classesListeners[$className][$eventName] ?? [] : self::$classesListeners[$className] ?? [] : self::$classesListeners;
    }

    public static function propagateClassEvent(Event $evt) {
        if (substr($evt->getSourceClass(), 0, 15) === 'class@anonymous') {
            return true;
        }
        if (!($listeners = &self::$classesListeners[$evt->getSourceClass()][$evt->getEventName()] ?? false)) {
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

        if (!count($listeners)) {
            unset($listeners);
        }

        return $res;
    }
}
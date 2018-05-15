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
            foreach ($listeners as $evtName => $cb) {
                if ((self::$classesListeners[$className][$evtName] ?? false) && self::$staticMaxListeners && count(self::$classesListeners[$className][$evtName]) === self::$staticMaxListeners) {
                    throw new Exception\EventEmitterGlobal("Maximum amount of listeners reached for event " . $evtName . " of " . $className);
                }

                if (is_callable($cb) || (is_array($cb) && count($cb) === 2 && is_string($cb[0]) && is_string($cb[1]))) {
                    self::$classesListeners[$className][$evtName][] = [false, &$cb,];
                }
                else {
                    throw new Exception\EventEmitterGlobal("Event callback has to be a callable or an array of two elements representing classname and method to call");
                }
            }
        }
    }

    public static function getListeners(?string $className = null) :array {
        return $className ? self::$classesListeners[$className] : self::$classesListeners;
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
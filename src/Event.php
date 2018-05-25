<?php
declare(strict_types=1);

/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


/**
 * Class Event
 *
 * @package xobotyi\emittr
 */
class Event
{
    /**
     * @var string
     */
    private $eventName;
    /**
     * @var mixed
     */
    private $payload;
    /**
     * @var bool
     */
    private $propagate = true;
    /**
     * @var string
     */
    private $sourceClass;
    /**
     * @var \xobotyi\emittr\EventEmitterOld
     */
    private $sourceObject;

    /**
     * Event constructor.
     *
     * @param string                               $evtName
     * @param mixed                                $payload
     * @param string|null                          $sourceClass
     * @param null|\xobotyi\emittr\EventEmitterOld $sourceObject
     */
    public function __construct(string $evtName, $payload = null, string $sourceClass = null, ?EventEmitterOld $sourceObject = null) {
        $this->eventName    = $evtName;
        $this->payload      = $payload;
        $this->sourceClass  = $sourceClass;
        $this->sourceObject = $sourceObject;
    }

    /**
     * @return \xobotyi\emittr\Event
     */
    public function stopPropagation() :self {
        $this->propagate = false;

        return $this;
    }

    /**
     * @return \xobotyi\emittr\Event
     */
    public function startPropagation() :self {
        $this->propagate = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPropagatable() :bool {
        return $this->propagate;
    }

    /**
     * @return string
     */
    public function getEventName() :string {
        return $this->eventName;
    }

    /**
     * @return mixed|null
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * @return null|\xobotyi\emittr\EventEmitterOld
     */
    public function getSourceObject() {
        return $this->sourceObject;
    }

    /**
     * @return null|string
     */
    public function getSourceClass() {
        return $this->sourceClass;
    }
}
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
     * @var object
     */
    private $sourceObject;

    /**
     * Event constructor.
     *
     * @param string      $evtName
     * @param mixed       $payload
     * @param string|null $sourceClass
     * @param object|null $sourceObject
     */
    public function __construct(string $evtName, $payload = null, string $sourceClass = null, $sourceObject = null) {
        $this->eventName    = $evtName;
        $this->payload      = $payload;
        $this->sourceClass  = $sourceClass;
        $this->sourceObject = $sourceObject;
    }

    /**
     * Stop the event propagation.
     *
     * @return \xobotyi\emittr\Event
     */
    public function stopPropagation() :self {
        $this->propagate = false;

        return $this;
    }

    /**
     * Resume the event propagation
     *
     * @return \xobotyi\emittr\Event
     */
    public function startPropagation() :self {
        $this->propagate = true;

        return $this;
    }

    /**
     * Check if event is propagatable
     *
     * @return bool
     */
    public function isPropagatable() :bool {
        return $this->propagate;
    }

    /**
     * Return the event name
     *
     * @return string
     */
    public function getEventName() :string {
        return $this->eventName;
    }

    /**
     * Return the event payload
     *
     * @return mixed|null
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * Return the object emitted the event
     *
     * @return object|null
     */
    public function getSourceObject() {
        return $this->sourceObject;
    }

    /**
     * Return the name of class emitted the event
     *
     * @return string|null
     */
    public function getSourceClass() :?string {
        return $this->sourceClass;
    }
}
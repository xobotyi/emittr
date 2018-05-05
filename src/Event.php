<?php
declare(strict_types=1);

/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


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
    private $src;
    /**
     * @var string
     */
    private $srcClass;

    public function __construct(string $evtName, $payload = null, string $sourceClass = null, ?EventEmitter $sourceEmitter = null) {
        $this->eventName = $evtName;
        $this->payload   = $payload;
        $this->srcClass  = $sourceClass;
        $this->src       = $sourceEmitter;
    }

    public function stopPropagation() :self {
        $this->propagate = false;

        return $this;
    }

    public function startPropagation() :self {
        $this->propagate = true;

        return $this;
    }

    public function isPropagatable() :bool {
        return $this->propagate;
    }

    public function getEventName() :string {
        return $this->eventName;
    }

    public function getPayload() {
        return $this->payload;
    }

    public function getSource() {
        return $this->src;
    }

    public function getSourceClass() {
        return $this->srcClass;
    }
}
<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEvent() {
        $evt = new Event('testEvent');

        $this->assertNull($evt->getPayload());
        $this->assertNull($evt->getSourceObject());
        $this->assertNull($evt->getSourceClass());

        $this->assertEquals($evt->getEventName(), 'testEvent');
        $this->assertTrue($evt->isPropagatable());

        $evt->stopPropagation();
        $this->assertFalse($evt->isPropagatable());

        $evt->startPropagation();
        $this->assertTrue($evt->isPropagatable());
    }
}
<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;

class EventEmitterTest extends TestCase
{
    public function testEventEmitter() {
        $ee = new class extends EventEmitter
        {
        };

        $ee->setMaxListeners(5);
        $this->assertEquals(5, $ee->getMaxListeners());
    }
}
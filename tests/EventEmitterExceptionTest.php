<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

use PHPUnit\Framework\TestCase;

class EventEmitterExceptionTest extends TestCase
{
    public function testException() {
        $this->expectException(Exception\EventEmitter::class);
        $this->expectExceptionMessage('Hello world');
        $this->expectExceptionCode(1);
        throw new Exception\EventEmitter('Hello world', 1);
    }
}
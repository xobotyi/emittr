<?php

/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;

use PHPUnit\Framework\TestCase;

class EventEmitterGlobalExceptionTest extends TestCase
{
    public function testException() {
        $this->expectException(Exception\EventEmitterGlobal::class);
        $this->expectExceptionMessage('Hello world');
        $this->expectExceptionCode(1);
        throw new Exception\EventEmitterGlobal('Hello world', 1);
    }
}
<?php
/**
 * @Author : a.zinovyev
 * @Package: emittr
 * @License: http://www.opensource.org/licenses/mit-license.php
 */

namespace xobotyi\emittr;


use PHPUnit\Framework\TestCase;

class EmitterTest extends EventEmitter
{
}

class EventEmitterGlobalTest extends TestCase
{
    public function testEventEmitterGlobal() {
        $ee = new EmitterTest();

        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test' => function (Event $e) { },
                                                          ],
                                                      ]);

        $this->assertEquals([
                                EmitterTest::class => [
                                    'test' => [[false, function (Event $e) { }]],
                                ],
                            ], EventEmitterGlobal::getListeners());

        EventEmitterGlobal::setMaxListeners(0);
        $this->assertEquals(0, EventEmitterGlobal::getMaxListeners());
    }

    public function testEventEmitterGlobalExceptionMaxListeners() {
        $ee = new EmitterTest();

        EventEmitterGlobal::setMaxListeners(2);

        $this->expectException(Exception\EventEmitterGlobal::class);
        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test' => [
                                                                  function (Event $e) { },
                                                                  function (Event $e) { },
                                                                  function (Event $e) { },
                                                              ],
                                                          ],
                                                      ]);
    }
}
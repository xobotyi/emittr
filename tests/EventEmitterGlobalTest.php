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
                                                              'test0'  => function (Event $e) { },
                                                              'fooBar' => ['Bar', 'baz'],
                                                              'boo'    => [
                                                                  function (Event $e) { },
                                                                  ['Bar', 'baz'],
                                                              ],
                                                          ],
                                                      ]);

        $this->assertEquals([
                                EmitterTest::class => [
                                    'test0'  => [[false, function (Event $e) { }]],
                                    'fooBar' => [[false, ['Bar', 'baz']]],
                                    'boo'    => [[false, function (Event $e) { }], [false, ['Bar', 'baz']]],
                                ],
                            ], EventEmitterGlobal::getListeners());

        EventEmitterGlobal::setMaxListeners(0);
        $this->assertEquals(0, EventEmitterGlobal::getMaxListeners());

        $ee->emit('test');
    }

    public function testEventEmitterGlobalExceptionMaxListeners() {
        EventEmitterGlobal::setMaxListeners(2);

        $this->expectException(Exception\EventEmitter::class);
        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test1' => [
                                                                  function (Event $e) { },
                                                                  function (Event $e) { },
                                                                  function (Event $e) { },
                                                              ],
                                                          ],
                                                      ]);
    }

    public function testEventEmitterGlobalExceptionNotCallable() {
        $this->expectException(Exception\EventEmitter::class);
        EventEmitterGlobal::loadClassesEventListeners([
                                                          EmitterTest::class => [
                                                              'test2' => [
                                                                  null,
                                                              ],
                                                          ],
                                                      ]);
    }
}
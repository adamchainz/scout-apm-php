<?php

declare(strict_types=1);

namespace Scoutapm\IntegrationTests;

use PHPUnit\Framework\TestCase;
use Scoutapm\Agent;
use Scoutapm\Config;
use Scoutapm\Connector\SocketConnector;
use function getenv;
use function json_decode;
use function json_encode;
use function sleep;

/** @coversNothing */
final class AgentTest extends TestCase
{
    public function testLoggingIsSent() : void
    {
        $scoutApmKey = getenv('SCOUT_APM_KEY');

        if ($scoutApmKey === false) {
            self::markTestSkipped('Set the environment variable SCOUT_APM_KEY to enable this test.');

            return;
        }

        $config = Config::fromArray([
            'name' => 'Agent Integration Test',
            'key' => $scoutApmKey,
            'monitor' => true,
        ]);

        $connector = new MessageCapturingConnectorDelegator(new SocketConnector($config->get('socket_path')));

        $agent = Agent::fromConfig($config, null, $connector);

        $agent->connect();

        $agent->webTransaction('Yay', static function () use ($agent) : void {
            $agent->instrument('test', 'foo', static function () : void {
            });
            $agent->instrument('test', 'foo2', static function () : void {
            });
            $agent->tagRequest('testtag', '1.23');
        });

        self::assertTrue($agent->send());

        // @todo check the format of this matches up with expectations
        self::markTestIncomplete(__METHOD__);
        self::assertEquals(
            [
                [
                    'Register' => [],
                ],
                [
                    'ApplicationEvent' => [],
                ],
                [
                    'BatchCommand' => [
                        'commands' => [
                            [
                                'StartRequest' => [],
                            ],
                            [
                                'StartSpan' => [],
                            ],
                            [
                                'StopSpan' => [],
                            ],
                            [
                                'StartSpan' => [],
                            ],
                            [
                                'StopSpan' => [],
                            ],
                            [
                                'TagRequest' => [],
                            ],
                            [
                                'StartSpan' => [],
                            ],
                            [
                                'StopSpan' => [],
                            ],
                            [
                                'FinishRequest' => [],
                            ],
                        ],
                    ],
                ],
            ],
            json_decode(json_encode($connector->sentMessages), true)
        );

        // @todo perform more assertions - did we actually successfully send payload in the right format, etc.?
    }
}
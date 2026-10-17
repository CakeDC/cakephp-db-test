<?php
declare(strict_types=1);

/**
 * Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2013 - 2023, Cake Development Corporation (https://www.cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace CakeDC\DbTest\Test\TestCase\TestSuite\Fixture\Extension;

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use CakeDC\DbTest\TestSuite\Fixture\Extension\PHPUnitPreparationStartedSubscriber;
use PHPUnit\Event\Code\Test as EventTest;
use PHPUnit\Event\Telemetry\Duration;
use PHPUnit\Event\Telemetry\GarbageCollectorStatus;
use PHPUnit\Event\Telemetry\HRTime;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Event\Telemetry\MemoryUsage;
use PHPUnit\Event\Telemetry\Snapshot;
use PHPUnit\Event\Test\PreparationStarted;
use PHPUnit\Event\Test\PreparationStartedSubscriber;
use PHPUnit\Framework\TestCase;

/**
 * PHPUnitPreparationStartedSubscriberTest
 */
class PHPUnitPreparationStartedSubscriberTest extends TestCase
{
    /**
     * @var \Cake\Database\Connection
     */
    protected Connection $connection;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = new class extends Connection {
            /**
             * @var array<string>
             */
            public array $calls = [];

            /**
             * @var bool
             */
            public bool $inTransaction = false;

            /**
             * Skip the parent constructor, the test never touches the driver.
             */
            public function __construct()
            {
            }

            /**
             * @return bool
             */
            public function inTransaction(): bool
            {
                return $this->inTransaction;
            }

            /**
             * @return void
             */
            public function begin(): void
            {
                $this->calls[] = 'begin';
                $this->inTransaction = true;
            }

            /**
             * @param bool|null $toBeginning Whether the transaction should be rolled back to the beginning.
             * @return bool
             */
            public function rollback(?bool $toBeginning = null): bool
            {
                $this->calls[] = 'rollback';
                $this->inTransaction = false;

                return true;
            }
        };

        ConnectionManager::drop('test');
        ConnectionManager::setConfig('test', $this->connection);
    }

    /**
     * The subscriber opens the per-test transaction on the PreparationStarted event.
     *
     * @return void
     */
    public function testOpensTransactionOnPreparationStarted(): void
    {
        $subscriber = new PHPUnitPreparationStartedSubscriber();
        $this->assertInstanceOf(PreparationStartedSubscriber::class, $subscriber);

        $subscriber->notify($this->preparationStartedEvent());

        $this->assertSame(['begin'], $this->connection->calls);
    }

    /**
     * A transaction left open by a previous test is rolled back before beginning.
     *
     * @return void
     */
    public function testRollsBackAbandonedTransactionBeforeBeginning(): void
    {
        $this->connection->inTransaction = true;

        (new PHPUnitPreparationStartedSubscriber())->notify($this->preparationStartedEvent());

        $this->assertSame(['rollback', 'begin'], $this->connection->calls);
    }

    /**
     * Builds a PreparationStarted event; the subscriber ignores the payload.
     *
     * @return \PHPUnit\Event\Test\PreparationStarted
     */
    protected function preparationStartedEvent(): PreparationStarted
    {
        $snapshot = new Snapshot(
            HRTime::fromSecondsAndNanoseconds(0, 0),
            MemoryUsage::fromBytes(0),
            MemoryUsage::fromBytes(0),
            new GarbageCollectorStatus(0, 0, 0, 0, null, null, null, null, null, null, null, null),
        );
        $telemetry = new Info(
            $snapshot,
            Duration::fromSecondsAndNanoseconds(0, 0),
            MemoryUsage::fromBytes(0),
            Duration::fromSecondsAndNanoseconds(0, 0),
            MemoryUsage::fromBytes(0),
        );
        $test = new class ('PreparationStarted') extends EventTest {
            /**
             * @return string
             */
            public function id(): string
            {
                return 'CakeDC\DbTest\Test\TestCase\PreparationStarted::test';
            }

            /**
             * @return string
             */
            public function name(): string
            {
                return 'test';
            }
        };

        return new PreparationStarted($telemetry, $test);
    }
}

<?php

namespace AshFieldJumper\DiscordLogger\Tests;

use DateTime;
use InvalidArgumentException;
use AshFieldJumper\DiscordLogger\Contracts\DiscordWebHook;
use AshFieldJumper\DiscordLogger\Logger;
use AshFieldJumper\DiscordLogger\Tests\Support\FakeDiscordWebHook;
use RuntimeException;

class LoggerTest extends TestCase
{

    /** @var \AshFieldJumper\DiscordLogger\Tests\Support\FakeDiscordWebHook */
    private $discordFake;

    /** @var \AshFieldJumper\DiscordLogger\Logger */
    private $logger;

    /** @var \Monolog\Logger */
    private $monolog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->discordFake = new FakeDiscordWebHook('http://example.com');
        $this->app->bind(DiscordWebHook::class, function () {
            return $this->discordFake;
        });

        $this->logger = $this->app->make(Logger::class);
        $this->monolog = ($this->logger)(['level' => 'INFO', 'url' => 'http://example.com']);
    }

    /** @test */
    public function throws_exception_if_url_missing_from_channel_configuration()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You must set the `url` key in your discord channel configuration');

        ($this->logger)([]);
    }

    /** @test */
    public function log_is_sent_to_discord()
    {
        $this->monolog->warning('This is a test');
        $this->discordFake->assertSendCount(1);
    }
}

<?php

namespace App\Tests;

class ServerFileTest extends AbstractTestCase
{
    /** @test */
    public function initialise_nginx_for_the_first_time_and_setup_returns_false()
    {
        $server = self::$container->get('App\Server\NginxServer');

        $this->assertFalse($server->isSetupComplete());
    }

    /** @test */
    public function create_nginx_http_block()
    {
        self::$container->get('App\Server\NginxServer')->setup();

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxHttpBlock.php', '/data/test/docker-setup/nginx/nginx.conf');
    }

    /** @test */
    public function create_nginx_server_block_for_non_ssl_only()
    {
        $this->runApp(['domain' => 'dev.example.com'], ['no', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxServerBlock.php', '/data/test/docker-setup/nginx/sites-enabled/dev.example.com.conf');
    }

    /** @test */
    public function create_nginx_server_block_for_ssl()
    {
        $this->runApp(['domain' => 'dev.example.com'], ['yes', '0', '0', 0, 'None']);

        $this->assertFileEquals(__DIR__.'/TestFiles/nginxServerBlockWithSSL.php', '/data/test/docker-setup/nginx/sites-enabled/dev.example.com.conf');
    }
}

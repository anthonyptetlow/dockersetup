version: "<?= $docker['version'] ?>"
services:
  nginx_<?= \App\Helper\Str::slug($domain) ?>:
    image: <?= $docker['services']['nginx']['image'] ?? 'nginx:latest' ?><?= "\n" ?>
    container_name: nginx_<?= \App\Helper\Str::slug($domain) ?><?= "\n" ?>
    volumes:
      - <?= $config['root_directory'] ?>/code/<?= $domain ?>:/var/www/<?= $domain ?><?= "\n" ?>
      - <?= $config['root_directory'] ?>/nginx/sites-enabled/<?= $domain ?>.conf:/etc/nginx/sites-enabled/<?= $domain ?>.conf
      - <?= $config['root_directory'] ?>/nginx/nginx.conf:/etc/nginx/nginx.conf
<?php if ($ssl) { ?>
      - <?= $config['root_directory'] ?>/tls/<?= $domain ?>.key:/etc/tls/<?= $domain ?>.key
      - <?= $config['root_directory'] ?>/tls/<?= $domain ?>.crt:/etc/tls/<?= $domain ?>.crt
<?php } ?>
    depends_on:
      - web_<?= \App\Helper\Str::slug($domain) ?><?= "\n" ?>
    networks:
      <?= $docker['network'] ?>:
        aliases:
          - <?= $domain ?><?= "\n" ?>
  db_<?= $database['type'] ?>:
    image: <?= $docker['services']['db'][$database['type']][$database['version']]['image'] ?><?= "\n" ?>
    container_name: db_<?= $database['type'] ?><?= "\n" ?>
    command: <?= $docker['services']['db'][$database['type']][$database['version']]['command'] ?><?= "\n" ?>
    ports:
      - <?= $docker['services']['db'][$database['type']][$database['version']]['port'] ?>:<?= $docker['services']['db'][$database['type']][$database['version']]['port'] ?><?= "\n" ?>
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_ROOT_HOST: db
      MYSQL_DATABASE: test
      MYSQL_USER: user
      MYSQL_PASSWORD: root
    volumes:
      - <?= $config['root_directory'] ?>/<?= $database['type'] ?>:/var/lib/<?= $database['type'] ?><?= "\n" ?>
    networks:
      - <?= $docker['network'] ?><?= "\n" ?>
  web_<?= \App\Helper\Str::slug($domain) ?>:
    image: <?= $docker['services']['web']['php']['7.4']['image'] ?><?= "\n" ?>
    container_name: web_<?= \App\Helper\Str::slug($domain) ?><?= "\n" ?>
    volumes:
      - <?= $config['root_directory'] ?>/code/<?= $domain ?>:/var/www/<?= $domain ?><?= "\n" ?>
      - <?= $config['root_directory'] ?>/php/upload.ini:/usr/local/etc/php/conf.d/upload.ini
      - <?= $config['root_directory'] ?>/php/opcache.ini:/usr/local/etc/php/conf.d/opcache.ini
      - <?= $config['root_directory'] ?>/php/xdebug.ini:/usr/local/etc/php/conf.d/xdebug.ini
    working_dir: <?= $config['root_directory'] ?>/code/<?= $domain ?><?= "\n" ?>
    environment:
    <?php foreach ($docker['services']['web']['environment'] as $env) { ?><?= "  " . key($env) ?>: <?= $env[key($env)]; ?><?= "\n" ?><?php } ?>
    networks:
      - <?= $docker['network'] ?><?= "\n" ?>

networks:
  <?= $docker['network'] ?>:

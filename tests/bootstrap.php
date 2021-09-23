<?php

declare(strict_types=1);

use Doctrine\Common\Annotations\AnnotationRegistry;

\define('TESTS_PATH', __DIR__);
\define('VENDOR_PATH', \dirname(__DIR__).'/vendor');

AnnotationRegistry::registerFile(
    __DIR__.'/../vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php'
);

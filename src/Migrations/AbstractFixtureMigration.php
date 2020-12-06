<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader as Loader;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Used to run fixtures in migrations
 *
 * @see https://gist.github.com/lavoiesl/77375da08b3274aa6440
 */
abstract class AbstractFixtureMigration extends AbstractMigration implements ContainerAwareInterface
{
    private ?ContainerInterface $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    /**
     * @param list<FixtureInterface> $fixtures
     */
    protected function loadFixtures(array $fixtures, bool $append = true): void
    {
        \assert(null !== $this->container);
        $em = $this->container->get('doctrine.orm.entity_manager');
        \assert($em instanceof EntityManagerInterface);

        $loader = new Loader($this->container);
        array_map(array($loader, 'addFixture'), $fixtures);

        $purger = null;
        if ($append === false) {
            $purger = new ORMPurger($em);
            $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        }

        $executor = new ORMExecutor($em, $purger);

        $output = new ConsoleOutput;
        $executor->setLogger(function($message) use ($output) {
            $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
        });

        $executor->execute($loader->getFixtures(), $append);
    }
}

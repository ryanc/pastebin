<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('Mmm Paste', 'master');

$console
    ->register('stats')
    ->setDefinition(array(
        // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
    ))
    ->setDescription('Print pastebin statistics')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
        // do something
        $count = $app['db']->fetchColumn('SELECT COUNT(*) FROM pastes');

        $output->writeln(sprintf("pastes: %d", $count));

        $count = $app['db']->fetchColumn('SELECT COUNT(*) FROM paste_content');

        $output->writeln(sprintf("paste_content: %d", $count));
    })
;

return $console;

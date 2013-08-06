<?php

/**
 * This file is part of the DigitalOcean library.
 *
 * (c) Antoine Corcy <contact@sbin.dk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DigitalOcean\CLI\Images;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use DigitalOcean\CLI\Command;

/**
 * Command-line images:transfert class.
 *
 * @author Antoine Corcy <contact@sbin.dk>
 */
class TransfertCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('images:transfert')
            ->setDescription('Transfert a specific image to a specified region id')
            ->addArgument('id', InputArgument::REQUIRED, 'The image id')
            ->addArgument('region_id', InputArgument::REQUIRED, 'The region id')
            ->addOption('credentials', null, InputOption::VALUE_REQUIRED,
                'If set, the yaml file which contains your credentials', COMMAND::DEFAULT_CREDENTIALS_FILE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getHelperSet()->get('dialog')->askConfirmation(
            $output,
            sprintf('<question>Are you sure to transfert this image %s to this new region id %s ? (y/N)</question> ',
                $input->getArgument('id'), $input->getArgument('region_id')),
            false
        )) {
            $output->writeln('Aborted!');

            return;
        }

        $digitalOcean = $this->getDigitalOcean($input->getOption('credentials'));
        $image        = $digitalOcean->images()->transfert(
            $input->getArgument('id'), array('region_id' => (int) $input->getArgument('region_id'))
        );

        $result[] = sprintf('status:   <value>%s</value>', $image->status);
        $result[] = sprintf('event_id: <value>%s</value>', $image->event_id);

        $output->getFormatter()->setStyle('value', new OutputFormatterStyle('green', 'black'));
        $output->writeln($result);
    }
}

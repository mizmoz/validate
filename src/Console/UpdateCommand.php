<?php
/**
 * @package mizmoz/validate
 * @copyright Copyright 2018 Mizmoz Limited - Released under the MIT license
 * @see https://www.mizmoz.com/labs/validate
 */

namespace Mizmoz\Validate\Console;

use Mizmoz\Validate\Console\Update\IsEmailDisposable;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('update')
            ->setDescription('Update the data files for the validation');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Updating the data files.');

        // write the files to
        $filePath = __DIR__ . '/../../resources/is-email-disposable.php';

        // update the disposable lists
        (new IsEmailDisposable())->update($filePath);

        return 0;
    }
}

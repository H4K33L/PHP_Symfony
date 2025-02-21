<?php

namespace App\Command;

use App\Service\HabitResetService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetHabitsCommand extends Command
{
    protected static $defaultName = 'app:reset-habits';

    private $habitResetService;

    public function __construct(HabitResetService $habitResetService)
    {
        $this->habitResetService = $habitResetService;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:reset-habits')
            ->setDescription('Reset daily habits for all users.')
            ->setHelp('This command resets the status of predefined daily habits for all users.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->habitResetService->resetDailyHabits();
        $output->writeln('Habits reset successfully.');
        return Command::SUCCESS;
    }
}
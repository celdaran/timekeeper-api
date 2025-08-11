<?php namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;

use App\Dto\AccountCreateRequest;
use App\Service\AccountService;

#[AsCommand(
    name: 'app:create-sysadmin',
    description: 'Create the system administrator account',
)]
class CreateSysadminAccountCommand extends Command
{
    public function __construct(private readonly AccountService $accountService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Get the QuestionHelper
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        // Ask for a password, with hidden input
        $accountQuestion = new Question('Please enter a new system administrator account name (UUID preferred): ');
        $account = $helper->ask($input, $output, $accountQuestion);

        $passwordQuestion = new Question('Please enter a new system administrator password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $passwordQuestion);

        $newAccount = new AccountCreateRequest();
        $newAccount->username = $account;
        $newAccount->password = $password;
        $newAccount->email = 'none@example.com';
        $newAccount->admin = true;
        $newAccount->description = 'System administrator account';

        try {
            $result = $this->accountService->create($newAccount, true);
            $io->success('Created system administrator account: ' . $result['account_id']);
            return Command::SUCCESS;
        }
        catch (Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }
}

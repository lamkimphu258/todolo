<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

#[AsCommand(
    name: 'app:author-weekly-report:send',
    description: 'Send weekly reports to authors',
)]
class AuthorWeeklyReportSendCommand extends Command
{
    public function __construct(
        private UserRepository $userRepository,
        private Mailer $mailer
    ) {
        parent::__construct(null);
    }

    protected function configure(): void
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $subscribers = $this->userRepository
            ->findAllSubscribedToNewsletter();
        $io->progressStart(count($subscribers));

        /** @var User $subscriber */
        foreach ($subscribers as $subscriber) {
            $io->progressAdvance();
            $this->mailer->sendWeeklyReport($subscriber);
        }

        $io->progressFinish();
        $io->success('Weekly report sent to subscribers');
        return Command::SUCCESS;
    }
}

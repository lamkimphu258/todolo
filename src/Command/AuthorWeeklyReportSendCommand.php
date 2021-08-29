<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
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
        protected UserRepository $userRepository,
        protected MailerInterface $mailer,
        protected Environment $twig,
        protected Pdf $pdf
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

        $html = $this->twig->render('email/weekly-report-pdf.html.twig');
        $pdf = $this->pdf->getOutputFromHtml($html);

        /** @var User $subscriber */
        foreach ($subscribers as $subscriber) {
            $io->progressAdvance();
            $email = (new TemplatedEmail())
                ->from(new Address('lamkimphu258@gmail.com', 'CEO Todolo'))
                ->to(new Address($subscriber->getEmail(), 'Subscriber'))
                ->subject('Your weekly report about our upgrades')
                ->htmlTemplate('email/weekly-report.html.twig')
                ->attach($pdf, sprintf('weekly-report-%s.pdf', date('Y-m-d')));
            $this->mailer->send($email);
        }

        $io->progressFinish();
        $io->success('Weekly report sent to subscribers');
        return Command::SUCCESS;
    }
}

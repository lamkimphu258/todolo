<?php

namespace App\Service;

use App\Entity\User;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;

class Mailer
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private Pdf $pdf
    ) {
    }

    public function sendWelcomeMessage(User $user)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('lamkimphu258@gmail.com', 'CEO Todolo'))
            ->to(new Address($user->getEmail()))
            ->subject('Welcome to Todolo')
            ->htmlTemplate('email/welcome.html.twig');

        $this->mailer->send($email);
    }

    public function sendWeeklyReport(User $subscriber)
    {
        $html = $this->twig->render('email/weekly-report-pdf.html.twig');
        $pdf = $this->pdf->getOutputFromHtml($html);

        $email = (new TemplatedEmail())
            ->from(new Address('lamkimphu258@gmail.com', 'CEO Todolo'))
            ->to(new Address($subscriber->getEmail(), 'Subscriber'))
            ->subject('Your weekly report about our upgrades')
            ->htmlTemplate('email/weekly-report.html.twig')
            ->attach($pdf, sprintf('weekly-report-%s.pdf', date('Y-m-d')));
        $this->mailer->send($email);
    }
}

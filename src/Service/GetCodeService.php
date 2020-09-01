<?php

namespace App\Service;

class GetCodeService
{
    const SERVERS = [
        '@mail' => '{imap.mail.ru:993/imap/ssl}',
        '@bk' => '{imap.mail.ru:993/imap/ssl}',
        '@list' => '{imap.mail.ru:993/imap/ssl}',
        '@rambler' => '{imap.rambler.ru:993/imap/ssl}',
    ];

    private string $email;
    private string $password;
    private ?string $emailServer;
    private array $emailFolders;

    public function __construct(string $email, string $password, ?string $emailServer)
    {
        $this->email = $email;
        $this->password = $password;
        $this->emailServer = $emailServer ?? $this->parseEmailServer($email);
        $this->emailFolders = $this->getEmailFolders();
    }

    protected function parseEmailServer(string $email): string
    {
        foreach (self::SERVERS as $server => $val) {
            $matches = [];
            if (preg_match("/$server/", $email, $matches)) {
                return $val;
            }
        }
        throw new \RuntimeException('Wrong email was given');
    }

    protected function getEmailFolders(): array
    {
        $imap = imap_open($this->emailServer, $this->email, $this->password);

        return imap_list($imap, $this->emailServer, '*');
    }

    public function getCode(int $amount, float $delay): string
    {
        for ($i = 0; $i < $amount; ++$i) {
            foreach ($this->emailFolders as $folder) {
                $imap = imap_open($folder, $this->email, $this->password);
                $msgs = $this->getMsgs($imap);

                foreach ($msgs as $msg) {
                    $header = imap_header($imap, $msg);
                    $body = imap_body($imap, $msg);

                    if ($this->checkMail($header->fromaddress)) {
                        imap_close($imap);
                        if ($code = $this->parseCode($header, $body)) {
                            return $code;
                        }
                    }
                }
                imap_close($imap);
            }

            sleep($delay);
        }

        return 'null';
    }

    protected function getMsgs($imap, string $mode = 'UNSEEN'): array
    {
        $msgs = imap_search($imap, $mode);

        return $msgs ? $msgs : [];
    }

    protected function checkMail(string $email, $pattern = '/instagram/'): bool
    {
        return (bool) preg_match($pattern, $email);
    }

    protected function parseCode($header, $body): ?string
    {
        $matches = [];
        preg_match('/>(\d{6})</', $body, $matches);
        preg_match('/(\d{6})/', $header->subject, $matches);
        if ($code = $matches[1] ?? false) {
            return $code;
        }

        return null;
    }
}

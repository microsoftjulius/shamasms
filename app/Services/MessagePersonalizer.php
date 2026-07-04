<?php

namespace App\Services;

class MessagePersonalizer
{
    /**
     * @param array{name?: string, var1?: string, var2?: string, var3?: string, var4?: string, var5?: string} $recipient
     */
    public function render(string $body, array $recipient): string
    {
        $tokens = [
            '@@name@@' => $recipient['name'] ?? '',
            '@@var1@@' => $recipient['var1'] ?? '',
            '@@var2@@' => $recipient['var2'] ?? '',
            '@@var3@@' => $recipient['var3'] ?? '',
            '@@var4@@' => $recipient['var4'] ?? '',
            '@@var5@@' => $recipient['var5'] ?? '',
        ];

        return strtr($body, $tokens);
    }

    public function segments(string $body): int
    {
        return max(1, (int) ceil(mb_strlen($body) / 160));
    }
}

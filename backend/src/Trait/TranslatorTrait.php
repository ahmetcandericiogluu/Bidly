<?php

namespace App\Trait;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorTrait
{
    protected function trans(string $id, array $parameters = [], ?string $domain = 'messages'): string
    {
        return $this->translator->trans($id, $parameters, $domain);
    }
}

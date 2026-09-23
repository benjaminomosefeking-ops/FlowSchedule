<?php


namespace App\Bundle\FlowScheduler\Domain\ValueObjects;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidInviteCodeException;


class VOBInviteCode
{
    private string $code;

    public function __construct(string $code)
    {
        if(strlen($code) !== 6 || !ctype_alnum($code)) { //ctype_alnum: para numeros y letras
            throw new InvalidInviteCodeException('El código de invitación debe tener exactamente 6 caracteres alfanuméricos.');
        }
        $this->code = strtolower($code); // convertir a minusculas
    }


    public function toString(): string
    {
        return $this->code;
    }

    //metodo equals
    public function equals(VOBInviteCode $other): bool
    {
        return $this->code === $other->toString();
    }

}
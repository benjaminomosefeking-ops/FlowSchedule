<?php

namespace App\Bundle\FlowScheduler\Domain\ValueObjects;
use App\Bundle\FlowScheduler\Domain\Exceptions\InvalidShiftException;

class VOBShiftId
{
    private string $nombre;

    public function __construct(string $nombre)
    {
        if (empty($nombre)) {
            throw new InvalidShiftException('El nombre del turno no es válido. Debe ser un string no vacío.');
        }

        if (!is_string($nombre)) {
            throw new InvalidShiftException('El nombre del turno no es válido. Debe ser un string.');
        }

        if (strlen($nombre) > 255) { //para nombres internacionales
            throw new InvalidShiftException('El nombre del turno no es válido. Debe tener un máximo de 255 caracteres.');
        }

        $this->nombre = $nombre;
    }


    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function equals(VOBShiftId $other): bool
    {
        return $this->nombre === $other->getNombre();
    }

}



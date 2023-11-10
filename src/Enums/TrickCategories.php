<?php

namespace App\Enums;

enum TrickCategories: string {
    case Mute = 'Mute';
    case Sad = 'Sad';
    case Indy = 'Indy';
    case Stalefish = 'Stalefish';
    case TailGrab = 'Tail Grab';
    case NoseGrab = 'Nose Grab';
    case Japan = 'Japan';
    case SeatBelt = 'Seat Belt';
    case TruckDriver = 'Truck Driver';

    public function toString(): string {
        return $this->value;
    }
}
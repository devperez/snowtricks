<?php

namespace App\Enums;

enum TrickCategories: string {
    case Grab = 'Grab';
    case Rotation = 'Rotation';
    case Flip = 'Flip';
    case Slide = 'Slide';
    case OneFoot = 'One Foot';
    case OldSchool = 'Old School';

    public function toString(): string {
        return $this->value;
    }
}
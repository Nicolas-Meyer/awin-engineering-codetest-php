<?php

namespace Awin\Tools\CoffeeBreak\Services\Notifiers\Interfaces;

use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;

interface NotifierInterface
{
    /**
     * @param StaffMember $staffMember
     * @param CoffeeBreakPreference[] $preference
     * @return bool
     */
    public function notifyStaffMember(StaffMember $staffMember, array $preference): bool;

}
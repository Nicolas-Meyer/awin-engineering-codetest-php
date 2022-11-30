<?php

namespace Awin\Tools\CoffeeBreak\Services\Notifiers\Interfaces;

use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;

interface NotifierInterface
{

    public function notifyStaffMember(StaffMember $staffMember, CoffeeBreakPreference $preference): bool;

}
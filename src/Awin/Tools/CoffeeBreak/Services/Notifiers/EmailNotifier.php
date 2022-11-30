<?php

namespace Awin\Tools\CoffeeBreak\Services\Notifiers;

use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;
use PHPUnit\Framework\MockObject\RuntimeException;

class EmailNotifier implements Interfaces\NotifierInterface
{
    /**
     * @param StaffMember $staffMember
     * @param CoffeeBreakPreference[] $preference
     * @return bool
     */
    public function notifyStaffMember(StaffMember $staffMember, array $preference): bool
    {

        if (empty($staffMember->getEmail())) {
            throw new RuntimeException("Cannot send notification - no email address");
        }

        return true;
    }
}
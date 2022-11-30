<?php
namespace Awin\Tools\CoffeeBreak\Services\Notifiers;

use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;
use Awin\Tools\CoffeeBreak\Services\Notifiers\Interfaces\NotifierInterface;
use PHPUnit\Framework\MockObject\RuntimeException;

class SlackNotifier implements NotifierInterface
{
    /**
     * @param StaffMember $staffMember
     * @param CoffeeBreakPreference[] $preference
     * @return bool
     */
    public function notifyStaffMember(StaffMember $staffMember, array $preference): bool
    {
        /**
         * Imagine that this function:
         * Sends a notification to the user on Slack that their coffee break refreshment today will be $preference
         * returns true of false status of notification sent
         */

        if (empty($staffMember->getSlackIdentifier())) {
            throw new RuntimeException("Cannot send notification - no SlackIdentifier");
        }

        return true;
    }
}
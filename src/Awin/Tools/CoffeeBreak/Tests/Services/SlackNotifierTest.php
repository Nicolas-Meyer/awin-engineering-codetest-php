<?php

use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;
use PHPUnit\Framework\TestCase;

class SlackNotifierTest extends TestCase
{
    public function testStatusOfNotificationIsTrue()
    {
        $staffMember = new StaffMember();
        $staffMember->setSlackIdentifier("ABC123");
        $preference[] = new CoffeeBreakPreference("drink", "coffee", $staffMember);

        $notificationService = new \Awin\Tools\CoffeeBreak\Services\Notifiers\SlackNotifier();
        $status = $notificationService->notifyStaffMember($staffMember, $preference);

        $this->assertTrue($status);
    }

    public function testThrowsExceptionWhenCannotNotify()
    {
        $staffMember = new StaffMember();
        $preference[] = new CoffeeBreakPreference("drink", "tea", $staffMember);
        $notificationService = new \Awin\Tools\CoffeeBreak\Services\Notifiers\SlackNotifier();

        $this->expectException(\RuntimeException::class);
        $status = $notificationService->notifyStaffMember($staffMember, $preference);
    }
}


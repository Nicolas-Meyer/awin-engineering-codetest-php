<?php

use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;
use PHPUnit\Framework\TestCase;

class EmailNotifierTest extends TestCase
{
    public function testStatusOfNotificationIsTrue()
    {
        $staffMember = new StaffMember();
        $staffMember->setEmail("employee@awin.com");
        $preference[] = new CoffeeBreakPreference("drink", "coffee", $staffMember);

        $notificationService = new \Awin\Tools\CoffeeBreak\Services\Notifiers\EmailNotifier();
        $status = $notificationService->notifyStaffMember($staffMember, $preference);

        $this->assertTrue($status);
    }

    public function testThrowsExceptionWhenCannotNotify()
    {
        $staffMember = new StaffMember();
        $preference[] = new CoffeeBreakPreference("drink", "tea", $staffMember);
        $notificationService = new \Awin\Tools\CoffeeBreak\Services\Notifiers\EmailNotifier();

        $this->expectException(\RuntimeException::class);
        $status = $notificationService->notifyStaffMember($staffMember, $preference);
    }
}


<?php

namespace Awin\Tools\CoffeeBreak\Tests\Controllers;

use Awin\Tools\CoffeeBreak\Controller\CoffeeBreakPreferenceController;
use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;
use Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository;
use Awin\Tools\CoffeeBreak\Repository\StaffMemberRepository;
use PHPUnit\Framework\TestCase;

class CoffeeBreakPreferenceControllerTest extends TestCase
{

    public function testTodayActionReturnsJson()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $response = $coffeeBreakPreferenceController->todayAction('json', $this->getDefaultCoffeeBreakPreferenceRepository());

        $expected = '[
        {
            "type": "food",
            "subType": "toast",
            "requestedBy": {
                "name": null,
                "SlackIdentifier": "ABC123",
                "email": null,
                "preferences": [
      
                ]
            },
            "requestedDate": null,
            "details": {
                   "flavour":false
            }
        }]';

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJsonString($expected, $response->getContent());
     }

    public function testTodayActionReturnsXml()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $response = $coffeeBreakPreferenceController->todayAction('xml', $this->getDefaultCoffeeBreakPreferenceRepository());

        $expected = '
            <response>
                <item key="0">
                    <type>food</type>
                    <subType>toast</subType>
                    <requestedBy>
                        <name/>
                        <email/>
                        <SlackIdentifier>ABC123</SlackIdentifier>
                        <preferences/>
                    </requestedBy>
                    <requestedDate/>
                    <details><flavour>0</flavour></details>
                </item>
            </response>';

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertXmlStringEqualsXmlString($expected, $response->getContent());

    }

    public function testTodayActionReturnsHtml()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $response = $coffeeBreakPreferenceController->todayAction('html', $this->getDefaultCoffeeBreakPreferenceRepository());

        $expected = "<ul><li> would like a toast (flavour : )</li></ul>";

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertStringContainsString($expected, $response->getContent());

    }

    public function testTodayActionDefaultsToHtml()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $response = $coffeeBreakPreferenceController->todayAction('', $this->getDefaultCoffeeBreakPreferenceRepository());

        $expected = "<ul><li> would like a toast (flavour : )</li></ul>";

        $this->assertEquals('200', $response->getStatusCode());
        $this->assertStringContainsString($expected, $response->getContent());
    }

    public function testTodayThrowsExceptionWhenUnknownFormat()
    {

        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $this->expectException(\InvalidArgumentException::class);
        $response = $coffeeBreakPreferenceController->todayAction('someFormat', $this->getDefaultCoffeeBreakPreferenceRepository());

    }

    public function testNotifyStaffMemberActionThroughEmail()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $response = $coffeeBreakPreferenceController->notifyStaffMemberAction(1, $this->getDefaultStaffMemberRepository("employee@awin.com", ""), $this->getDefaultCoffeeBreakPreferenceRepository());
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testNotifyStaffMemberActionThroughSlack()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $response = $coffeeBreakPreferenceController->notifyStaffMemberAction(1, $this->getDefaultStaffMemberRepository("", "ABC123"), $this->getDefaultCoffeeBreakPreferenceRepository());
        $this->assertEquals('200', $response->getStatusCode());
    }

    public function testNotifyStaffMemberStaffMemberNotFound()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();

        $staffMemberRepository = $this->createMock(StaffMemberRepository::class);
        $staffMemberRepository->expects(self::once())
            ->method('find')
            ->willReturn(null);

        $response = $coffeeBreakPreferenceController->notifyStaffMemberAction(1, $staffMemberRepository, $this->createMock(CoffeeBreakPreferenceRepository::class));
        $this->assertEquals('422', $response->getStatusCode());
    }

    public function testNotifyStaffMemberPreferenceNotFound()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();


        $response = $coffeeBreakPreferenceController->notifyStaffMemberAction(1, $this->getDefaultStaffMemberRepository("", "ABC123"), $this->createMock(CoffeeBreakPreferenceRepository::class));
        $this->assertEquals('422', $response->getStatusCode());
    }

    public function testNotifyStaffMemberActionNoNotificationPreference()
    {
        $coffeeBreakPreferenceController = new CoffeeBreakPreferenceController();
        $this->expectException(\RuntimeException::class);
        $response = $coffeeBreakPreferenceController->notifyStaffMemberAction(1, $this->getDefaultStaffMemberRepository("", ""), $this->getDefaultCoffeeBreakPreferenceRepository());
    }


    private function getDefaultCoffeeBreakPreferenceRepository()
    {

        $staffMember = new StaffMember();
        $staffMember->setSlackIdentifier("ABC123");

        $result[] = new CoffeeBreakPreference('food','toast', $staffMember);

        $coffeeBreakPreferenceRepository = $this->createMock(CoffeeBreakPreferenceRepository::class);
        $coffeeBreakPreferenceRepository->expects(self::once())
            ->method('getPreferencesForToday')
            ->willReturn($result);

        return $coffeeBreakPreferenceRepository;

    }

    private function getDefaultStaffMemberRepository($email, $slackIdentifier)
    {
        $staffMemberRepository = $this->createMock(StaffMemberRepository::class);

        $staffMember = new StaffMember();
        $staffMember->setEmail($email);
        $staffMember->setSlackIdentifier($slackIdentifier);

        $staffMemberRepository->expects(self::once())
            ->method('find')
            ->willReturn($staffMember);

        return $staffMemberRepository;
    }

}

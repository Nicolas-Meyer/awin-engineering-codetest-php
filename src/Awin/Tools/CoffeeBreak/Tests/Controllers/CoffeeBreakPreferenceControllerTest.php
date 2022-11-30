<?php

namespace Awin\Tools\CoffeeBreak\Tests\Controllers;

use Awin\Tools\CoffeeBreak\Controller\CoffeeBreakPreferenceController;
use Awin\Tools\CoffeeBreak\Entity\CoffeeBreakPreference;
use Awin\Tools\CoffeeBreak\Entity\StaffMember;
use Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository;
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

}

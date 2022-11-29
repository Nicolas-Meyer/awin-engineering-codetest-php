<?php
namespace Awin\Tools\CoffeeBreak\Controller;

use Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository;
use Awin\Tools\CoffeeBreak\Repository\StaffMemberRepository;
use Awin\Tools\CoffeeBreak\Services\SlackNotifier;
use Symfony\Component\HttpFoundation\Response;

class CoffeeBreakPreferenceController
{

    /**
     * Publishes the list of preferences in the requested format
     */
    public function todayAction($format, CoffeeBreakPreferenceRepository $coffeeBreakPreferenceRepository): Response
    {

        if ('' == $format || null == $format) {
            $format = 'html';
        }

        $preferencesForToday = $coffeeBreakPreferenceRepository->getPreferencesForToday();

        switch ($format) {
            case "json":
                $responseContent = $this->getJsonForResponse($preferencesForToday);
                $contentType = "application/json";
                break;

            case "xml":
                $responseContent = $this->getXmlForResponse($preferencesForToday);
                $contentType = "text/xml";
                break;

            case "html":
                $responseContent = $this->getHtmlForResponse($preferencesForToday);
                $contentType = "text/html";
                break;

            default:
                throw new \InvalidArgumentException("Unrecognised format");
        }

        return new Response($responseContent, 200, ['Content-Type' => $contentType]);
    }

    /**
     * @param int $staffMemberId
     * @return Response
     */
    public function notifyStaffMemberAction(int $staffMemberId, StaffMemberRepository $staffMemberRepository, CoffeeBreakPreferenceRepository $coffeeBreakPreferenceRepository): Response
    {
        $staffMember = $staffMemberRepository->find($staffMemberId);

        $preference = $coffeeBreakPreferenceRepository->getPreferencesForToday($staffMemberId);

        $notifier = new SlackNotifier();
        $notificationSent = $notifier->notifyStaffMember($staffMember, $preference);

        return new Response($notificationSent ? "OK" : "NOT OK", 200);
    }

    private function getJsonForResponse(array $preferences)
    {
        return json_encode([
            "preferences" => array_map(
                function ($preference) {
                    return $preference->getAsArray();
                },
                $preferences
            )
        ]);
    }

    private function getXmlForResponse(array $preferences)
    {
        $preferencesNode = new \SimpleXMLElement("preferences");
        foreach ($preferences as $preference) {
            $preferencesNode->addChild($preference->getAsXmlNode());
        }

        return $preferencesNode->asXML();
    }

    private function getHtmlForResponse(array $preferences): string
    {
        $html = "<ul>";
        foreach ($preferences as $preference) {
            $html .= $preference->getAsListElement();
        }
        $html .= "</ul>";
        return $html;
    }
}

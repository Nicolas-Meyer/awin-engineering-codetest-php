<?php
namespace Awin\Tools\CoffeeBreak\Controller;

use Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository;
use Awin\Tools\CoffeeBreak\Repository\StaffMemberRepository;
use Awin\Tools\CoffeeBreak\Services\SlackNotifier;
use Symfony\Component\HttpFoundation\Response;

class CoffeeBreakPreferenceController
{
    public function __construct()
    {
    }

    /**
     * Publishes the list of preferences in the requested format
     */
    public function todayAction($format = "html"): Response
    {
        $repository = new CoffeeBreakPreferenceRepository();
        $preferencesForToday = $repository->getPreferencesForToday();

        $formattedPreferences = [];
        $contentType = "text/html";

        switch ($format) {
            case "json":
                $responseContent = $this->getJsonForResponse($preferencesForToday);
                $contentType = "application/json";
                break;

            case "xml":
                $responseContent = $this->getXmlForResponse($preferencesForToday);
                $contentType = "text/xml";
                break;

            default:
                $formattedPreferences[] = $this->getHtmlForResponse($preferencesForToday);
        }

        return new Response($responseContent, 200, ['Content-Type' => $contentType]);
    }

    /**
     * @param int $staffMemberId
     * @return Response
     */
    public function notifyStaffMemberAction(int $staffMemberId): Response
    {
        $staffMemberRepository = new StaffMemberRepository();
        $staffMember = $staffMemberRepository->find($staffMemberId);

        $repository = new CoffeeBreakPreferenceRepository();
        $preference = $repository->getPreferenceFor($staffMemberId, new \DateTime());

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

<?php
namespace Awin\Tools\CoffeeBreak\Controller;

use Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository;
use Awin\Tools\CoffeeBreak\Repository\StaffMemberRepository;
use Awin\Tools\CoffeeBreak\Services\Notifiers\SlackNotifier;
use PHPUnit\Framework\MockObject\RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CoffeeBreakPreferenceController
{

    /**
     * Publishes the list of preferences in the requested format
     */
    public function todayAction($format, CoffeeBreakPreferenceRepository $coffeeBreakPreferenceRepository): Response
    {

        if (empty($format)) {
            $format = 'html';
        }

        $preferencesForToday = $coffeeBreakPreferenceRepository->getPreferencesForToday();
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        switch ($format) {
            case "json":
                $responseContent = $serializer->serialize($preferencesForToday, 'json',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['asListElement', 'preferredNotifier']]);
                $contentType = "application/json";
                break;

            case "xml":
                $responseContent = $serializer->serialize($preferencesForToday, 'xml',
                    [AbstractNormalizer::IGNORED_ATTRIBUTES => ['asListElement', 'preferredNotifier']]);
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

        if (!$staffMember) {
            return new Response("Invalid staff member", 422);
        }

        $preference = $coffeeBreakPreferenceRepository->getPreferencesForToday($staffMemberId);

        if (!$preference) {
            return new Response("Invalid preference for this staff member today", 422);
        }

        $notifier = $staffMember->getPreferredNotifier();

        if (!$notifier) {
            throw new RuntimeException("Cannot send notification - no way to contact staff member");
        }

        $notificationSent = $notifier->notifyStaffMember($staffMember, $preference);

        //This will always be true in the current implementation
        return new Response($notificationSent ? "OK" : "NOT OK", 200);
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

<?php
namespace Awin\Tools\CoffeeBreak\Controller;

use Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository;
use Awin\Tools\CoffeeBreak\Repository\StaffMemberRepository;
use Awin\Tools\CoffeeBreak\Services\Notifiers\SlackNotifier;
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
                $responseContent = $serializer->serialize($preferencesForToday, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['asListElement']]);
                $contentType = "application/json";
                break;

            case "xml":
                $responseContent = $serializer->serialize($preferencesForToday, 'xml', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['asListElement']]);
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

        $preference = $coffeeBreakPreferenceRepository->getPreferencesForToday($staffMemberId)->toArray();

        $notifier = new SlackNotifier();
        $notificationSent = $notifier->notifyStaffMember($staffMember, $preference);

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

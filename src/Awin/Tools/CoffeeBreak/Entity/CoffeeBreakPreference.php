<?php
namespace Awin\Tools\CoffeeBreak\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("coffee_break_preference")
 * @ORM\Entity(repositoryClass="Awin\Tools\CoffeeBreak\Repository\CoffeeBreakPreferenceRepository")
 */
class CoffeeBreakPreference
{

    const TYPES = ["food", "drink"];
    const DRINK_TYPES = ["coffee", "tea"];
    const FOOD_TYPES = ["sandwich", "crisps", "toast"];

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="type", length=255)
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(name="sub_type", length=255);
     * @var string
     */
    private $subType;

    /**
     * @ORM\ManyToOne(targetEntity="Awin\Tools\CoffeeBreak\Entity\StaffMember", inversedBy="preferences")
     * @ORM\JoinColumn(name="requested_by", referencedColumnName="id")
     * @var StaffMember
     */
    private $requestedBy;

    /**
     * @ORM\Column(name="requested_date", type="datetime")
     * @var \DateTime
     */
    private $requestedDate;

    /**
     * @ORM\Column(name="details", type="json")
     * @var array
     */
    private $details = "";

    public function __construct($type, $subType, StaffMember $requestedBy, array $details = [])
    {
        if (!in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException;
        }

        if ($type == "food") {
            if (!in_array($subType, self::FOOD_TYPES)) {
                throw new \InvalidArgumentException;
            }
        } else {
            if (!in_array($subType, self::DRINK_TYPES)) {
                throw new \InvalidArgumentException;
            }
        }

        $this->type = $type;
        $this->subType = $subType;
        $this->requestedBy = $requestedBy;
        $this->setDetails($details);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSubType(): string
    {
        return $this->subType;
    }

    /**
     * @param string $subType
     */
    public function setSubType(string $subType)
    {
        $this->subType = $subType;
    }

    /**
     * @return StaffMember
     */
    public function getRequestedBy(): StaffMember
    {
        return $this->requestedBy;
    }

    /**
     * @param StaffMember $requestedBy
     */
    public function setRequestedBy(StaffMember $requestedBy)
    {
        $this->requestedBy = $requestedBy;
    }

    /**
     * @return \DateTime
     */
    public function getRequestedDate(): ? \DateTime
    {
        return $this->requestedDate;
    }

    /**
     * @param \DateTime $requestedDate
     */
    public function setRequestedDate(\DateTime $requestedDate)
    {
        $this->requestedDate = $requestedDate;
    }



    public function setDetails(array $details)
    {
        $parsedDetails = [];

        if ($this->type == "drink") {
            $parsedDetails["number_of_sugars"] = isset($details["number_of_sugars"]) ?? 0;
            $parsedDetails["milk"] = isset($details["milk"]) ?? false;
        } else {
            $parsedDetails["flavour"] = isset($details["flavour"]) ?? "don't mind";
        }

        $this->details = json_encode($parsedDetails);
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

//    public function getAsXmlElement(): string
//    {
//        $xml = "<preference type='".$this->getType()."' subtype='".$this->getSubType()."'>";
//        $xml .= "<requestedBy>".$this->getRequestedBy()->getName()."</requestedBy>";
//        $xml .= "<details>".$this->getDetails()."</details>";
//        $xml .= "</preference>";
//        return $xml;
//    }
//
//    public function getAsArray(): array
//    {
//        return [
//            "type" => $this->getType(),
//            "subType" => $this->getSubType(),
//            "requestedBy" => [
//                "name" => $this->getRequestedBy()->getName()
//            ],
//            "details" => $this->getDetails()
//        ];
//    }
//
//
//    public function getAsListElement(): string
//    {
//        $details = json_decode($this->getDetails(), true);
//
//        $detailsString = implode(
//            ",",
//            array_map(
//                function ($detailKey, $detailValue) {
//                    return "$detailKey : $detailValue";
//                },
//                array_keys($details),
//                array_values($details)
//            )
//        );
//        return "<li>".$this->getRequestedBy()->getName()." would like a ".$this->getSubtype()." ($detailsString)</li>";
//    }
}

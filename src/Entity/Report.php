<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\ORM\Mapping\Traits\CreatedAt;
use App\Doctrine\ORM\Mapping\Traits\Identity;
use App\Entity\Enum\Financing;
use App\Entity\Enum\ReportType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Report
{
    use Identity;
    use CreatedAt;

    private const START_YEAR = 2000;

    /**
     * @var string
     *
     * @Assert\NotBlank
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var ReportType
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="report_type_enum")
     */
    private $type;

    /**
     * @var Financing
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="financing_enum")
     */
    private $financing;

    /**
     * @var int
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer")
     */
    private $month;

    /**
     * @var int
     *
     * @Assert\NotBlank
     *
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Url
     *
     * @ORM\Column
     */
    private $url;

    public static function allowedYears(): array
    {
        return range(self::START_YEAR, (new \DateTime('+1 year'))->format('Y'));
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getType(): ?ReportType
    {
        return $this->type;
    }

    public function setType(?ReportType $type): void
    {
        $this->type = $type;
    }

    public function getFinancing(): ?Financing
    {
        return $this->financing;
    }

    public function setFinancing(?Financing $financing): void
    {
        $this->financing = $financing;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(?int $month): void
    {
        $this->month = $month;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}

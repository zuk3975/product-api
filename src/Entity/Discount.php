<?php

namespace App\Entity;

use App\Exception\AppException;
use App\Repository\DiscountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{
    public const string TARGET_TYPE_PRODUCT = 'product';
    public const string TARGET_TYPE_CATEGORY = 'category';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    //@todo: validation for percent (0-100)
    #[ORM\Column]
    private ?int $percent = null;

    //@todo: validation for target_type (must be either TYPE_PRODUCT or TYPE_CATEGORY)
    #[ORM\Column(length: 255)]
    private ?string $target_type = null;

    #[ORM\Column]
    private ?int $target_id = null;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getPercent(): ?int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function getTargetType(): ?string
    {
        return $this->target_type;
    }

    public function setTargetType(?string $target_type): void
    {
        if (!in_array($target_type, [self::TARGET_TYPE_PRODUCT, self::TARGET_TYPE_CATEGORY], true)) {
            throw new AppException('Invalid discount targetType');
        }

        $this->target_type = $target_type;
    }

    public function getTargetId(): ?int
    {
        return $this->target_id;
    }

    public function setTargetId(?int $target_id): void
    {
        $this->target_id = $target_id;
    }

    public function isApplicable(Product $product): bool
    {
        if ($this->getTargetType() === self::TARGET_TYPE_PRODUCT && $this->getTargetId() === $product->getId()) {
            return true;
        }

        return $this->getTargetType() === self::TARGET_TYPE_CATEGORY && $product->getCategory()->getId() === $this->getTargetId();
    }
}

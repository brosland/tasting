<?php

declare(strict_types=1);

namespace App\Standards\Domain\Standard;

use App\Common\Domain\Event\EventStorage;
use App\Common\Domain\Event\EventStorageProvider;
use App\Common\Domain\Language\Language;
use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Standard\Ics\StandardIcs;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;

class Standard implements EventStorageProvider
{
	use EventStorage;

	protected int $pk;
	private StandardId $id;
	private StandardType $type;
	private int $catalogueNumber;
	private string $code, $title, $description;
	private ?Language $language;
	private ?DateTimeImmutable $publicationDate, $approvalDate, $effectiveDate, $withdrawalDate, $announcementDate;
	private bool $isValid, $postProcessRequired = false;
	private string $sourceHash = '';
	private ?self $parent = null;
	/** @var Collection<string,StandardIcs> */
	private Collection $ics;
	/** @var Collection<int,self> */
	private Collection $revisions;

	public function __construct(
		StandardId $id,
		StandardType $type,
		int $catalogueNumber,
		string $code,
		string $title,
		string $description,
		?Language $language,
		?DateTimeImmutable $publicationDate,
		?DateTimeImmutable $approvalDate,
		?DateTimeImmutable $effectiveDate,
		?DateTimeImmutable $withdrawalDate,
		?DateTimeImmutable $announcementDate,
		bool $isValid
	) {
		$this->id = $id;
		$this->type = $type;
		$this->catalogueNumber = $catalogueNumber;
		$this->code = $code;
		$this->title = $title;
		$this->description = $description;
		$this->language = $language;
		$this->publicationDate = $publicationDate;
		$this->approvalDate = $approvalDate;
		$this->effectiveDate = $effectiveDate;
		$this->withdrawalDate = $withdrawalDate;
		$this->announcementDate = $announcementDate;
		$this->revisions = new ArrayCollection();
		$this->isValid = $isValid;
		$this->ics = new ArrayCollection();
	}

	public function getPk(): int
	{
		return $this->pk;
	}

	public function getId(): StandardId
	{
		return $this->id;
	}

	public function getType(): StandardType
	{
		return $this->type;
	}

	public function setType(StandardType $type): void
	{
		$this->type = $type;
	}

	public function getCatalogueNumber(): int
	{
		return $this->catalogueNumber;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function getLanguage(): ?Language
	{
		return $this->language;
	}

	public function setLanguage(?Language $language): void
	{
		$this->language = $language;
	}

	public function getPublicationDate(): ?DateTimeImmutable
	{
		return $this->publicationDate;
	}

	public function setPublicationDate(?DateTimeImmutable $publicationDate): void
	{
		$this->publicationDate = $publicationDate;
	}

	public function getApprovalDate(): ?DateTimeImmutable
	{
		return $this->approvalDate;
	}

	public function setApprovalDate(?DateTimeImmutable $approvalDate): void
	{
		$this->approvalDate = $approvalDate;
	}

	public function getEffectiveDate(): ?DateTimeImmutable
	{
		return $this->effectiveDate;
	}

	public function setEffectiveDate(?DateTimeImmutable $effectiveDate): void
	{
		$this->effectiveDate = $effectiveDate;
	}

	public function getWithdrawalDate(): ?DateTimeImmutable
	{
		return $this->withdrawalDate;
	}

	public function setWithdrawalDate(?DateTimeImmutable $withdrawalDate): void
	{
		$this->withdrawalDate = $withdrawalDate;
	}

	public function getAnnouncementDate(): ?DateTimeImmutable
	{
		return $this->announcementDate;
	}

	public function setAnnouncementDate(?DateTimeImmutable $announcementDate): void
	{
		$this->announcementDate = $announcementDate;
	}

	public function isValid(): bool
	{
		return $this->isValid;
	}

	public function setIsValid(bool $isValid): void
	{
		$this->isValid = $isValid;
	}

	public function isPostProcessRequired(): bool
	{
		return $this->postProcessRequired;
	}

	public function setPostProcessRequired(bool $postProcessRequired): void
	{
		$this->postProcessRequired = $postProcessRequired;
	}

	public function getSourceHash(): string
	{
		return $this->sourceHash;
	}

	public function setSourceHash(string $sourceHash): void
	{
		if ($this->sourceHash !== $sourceHash) {
			$this->postProcessRequired = true;
		}

		$this->sourceHash = $sourceHash;
	}

	public function getParent(): ?Standard
	{
		return $this->parent;
	}

	// ics *******************************************************************************

	/**
	 * @return array<StandardIcs>
	 */
	public function getIcs(): array
	{
		return $this->ics->toArray();
	}

	public function addIcs(IcsCode $icsCode): void
	{
		if (!$this->ics->containsKey($icsCode->toString())) {
			$this->ics->set($icsCode->toString(), new StandardIcs($this, $icsCode));
		}
	}

	public function removeIcs(IcsCode $icsCode): void
	{
		if ($this->ics->containsKey($icsCode->toString())) {
			$this->ics->remove($icsCode->toString());
		}
	}

	// revisions *************************************************************************

	/**
	 * @return array<self>
	 */
	public function getRevisions(): array
	{
		return $this->revisions->toArray();
	}

	public function addRevision(self $revision): void
	{
		if ($this === $revision) {
			throw new InvalidArgumentException('Revision cannot be the same as the standard.');
		}

		if ($this->type !== StandardType::Original) {
			throw new InvalidArgumentException('Only original standard can have revisions.');
		}

		if ($revision->type !== StandardType::Revision) {
			throw new InvalidArgumentException('Only revision standard can be added as revision.');
		}

		if (!$this->revisions->contains($revision)) {
			if ($revision->parent !== null) {
				throw new InvalidArgumentException('Revision cannot have multiple parents.');
			}

			$this->revisions->add($revision);

			$revision->parent = $this;
		}
	}

	public function removeRevision(self $revision): void
	{
		$this->revisions->removeElement($revision);

		$revision->parent = null;
	}
}

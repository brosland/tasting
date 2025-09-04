<?php

declare(strict_types=1);

namespace App\Standards\Domain\Category;

use App\Common\Domain\Event\EventStorage;
use App\Common\Domain\Event\EventStorageProvider;
use App\Common\Domain\Metadata\Metadata;
use Gedmo\Tree\Node;

class Category implements EventStorageProvider, Node
{
	use EventStorage, Metadata;

	protected int $pk;
	private CategoryId $id;
	private string $title;
	private ?string $externalId = null;
	# tree *********************************************************************
	protected self $root;
	protected ?self $parent, $sibling = null;
	protected int $treeLevel = 0, $treeLeft = 1, $treeRight = 2;
	protected int $version = 1;

	public function __construct(
		CategoryId $id,
		string $title,
		?self $parent = null,
		?string $externalId = null
	) {
		$this->id = $id;
		$this->title = $title;
		$this->parent = $parent;
		$this->externalId = $externalId;
	}

	public function getPk(): int
	{
		return $this->pk;
	}

	public function getId(): CategoryId
	{
		return $this->id;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function getExternalId(): ?string
	{
		return $this->externalId;
	}

	public function setExternalId(?string $externalId): void
	{
		$this->externalId = $externalId;
	}

	// tree ********************************************************************

	public function getRoot(): self
	{
		return $this->root;
	}

	public function getParent(): ?self
	{
		return $this->parent;
	}

	public function setParent(?self $parent): void
	{
		$this->parent = $parent;
	}

	public function getSibling(): ?self
	{
		return $this->sibling;
	}

	public function setSibling(?self $node): void
	{
		$this->sibling = $node;
	}

	public function getTreeLevel(): int
	{
		return $this->treeLevel;
	}

	public function getTreeLeft(): int
	{
		return $this->treeLeft;
	}

	public function getTreeRight(): int
	{
		return $this->treeRight;
	}

	public function getSubBlockCount(): int
	{
		return (int)floor(($this->treeRight - $this->treeLeft) / 2);
	}
}

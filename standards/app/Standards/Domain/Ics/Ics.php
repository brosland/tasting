<?php

declare(strict_types=1);

namespace App\Standards\Domain\Ics;

use App\Common\Domain\Event\EventStorage;
use App\Common\Domain\Event\EventStorageProvider;
use Gedmo\Tree\Node;

class Ics implements EventStorageProvider, Node
{
	use EventStorage;

	private IcsCode $code;
	private string $title, $description;
	# tree *********************************************************************
	protected self $root;
	protected ?self $parent, $sibling = null;
	protected int $treeLevel = 0, $treeLeft = 1, $treeRight = 2;
	protected int $version = 1;

	public function __construct(
		IcsCode $code,
		string $title,
		string $description,
		?self $parent = null
	) {
		$this->code = $code;
		$this->title = $title;
		$this->description = $description;
		$this->parent = $parent;
	}

	public function getCode(): IcsCode
	{
		return $this->code;
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

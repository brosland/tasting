<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Domain;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

final readonly class UnmsStandardPageParser
{
	const string STANDARD_PAGE_URL_REGEXP = '/^\/norma\/(\d+)\/.*$/';

	private DOMDocument $document;
	private DOMXPath $xpath;

	public function __construct(string $html)
	{
		$this->document = new DOMDocument();
		@$this->document->loadHTML($html);
		$this->document->normalizeDocument();

		$this->xpath = new DOMXPath($this->document);
	}

	/**
	 * @return array<int>
	 */
	public function parseRevisions(): array
	{
		$query = '//td[contains(@class, "title") and normalize-space(text())="Zmeny:"]/following-sibling::td[1]//a';

		return $this->parseStandardCatalogueNumberFromHref($query);
	}

	/**
	 * @return array<int>
	 */
	public function parseReplacedStandards(): array
	{
		$query = '//td[contains(@class, "title") and normalize-space(text())="Nahradené normy:"]/following-sibling::td[1]//a';

		return $this->parseStandardCatalogueNumberFromHref($query);
	}

	/**
	 * @return array<int>
	 */
	public function parseReplacementStandards(): array
	{
		$query = '//td[contains(@class, "title") and normalize-space(text())="Nahradzujúce normy:"]/following-sibling::td[1]//a';

		return $this->parseStandardCatalogueNumberFromHref($query);
	}

	/**
	 * @return array<int>
	 */
	private function parseStandardCatalogueNumberFromHref(string $query): array
	{
		/** @var DOMNodeList<DOMElement>|false $links */
		$links = $this->xpath->query($query);

		if ($links === false) {
			return [];
		}

		$numbers = [];

		foreach ($links as $link) {
			$href = $link->getAttribute('href');
			/** @var string|null $catalogueNumber */
			$catalogueNumber = preg_filter(self::STANDARD_PAGE_URL_REGEXP, '$1', $href, 1);

			if ($catalogueNumber !== null) {
				$numbers[] = (int)$catalogueNumber;
			}
		}

		return $numbers;
	}
}

<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Di;

use App\Standards\Providers\Unms\Domain\UnmsConnection;
use App\Standards\Providers\Unms\Services\UnmsStandardSyncService;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

final class UnmsExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'baseUri' => Expect::string('https://normy.normoff.gov.sk')
		])->castTo('array');
	}

	public function loadConfiguration(): void
	{
		parent::loadConfiguration();

		$this->compiler->loadDefinitionsFromConfig(
			$this->loadFromFile(__DIR__ . '/unms.config.neon')
		);
	}

	public function beforeCompile(): void
	{
		parent::beforeCompile();

		/** @var array<string,mixed> $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		/** @var ServiceDefinition $unmsConnection */
		$unmsConnection = $builder->getDefinitionByType(UnmsConnection::class);
		$unmsConnection->setArgument('baseUri', $config['baseUri']);

		/** @var ServiceDefinition $unmsStandardSyncService */
		$unmsStandardSyncService = $builder->getDefinitionByType(UnmsStandardSyncService::class);
		$unmsStandardSyncService->setArgument('icsCodes', $this->loadFromFile(__DIR__ . '/ics.neon'));
	}
}

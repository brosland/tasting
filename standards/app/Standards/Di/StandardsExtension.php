<?php

declare(strict_types=1);

namespace App\Standards\Di;

use Nette\DI\CompilerExtension;

final class StandardsExtension extends CompilerExtension
{
	public function loadConfiguration(): void
	{
		parent::loadConfiguration();

		$this->compiler->loadDefinitionsFromConfig(
			$this->loadFromFile(__DIR__ . '/standards.config.neon')
		);
	}

	public function beforeCompile(): void
	{
		parent::beforeCompile();

		/** @var array<string,mixed> $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

//		/** @var ServiceDefinition $authorizator */
//		$authorizator = $builder->getDefinitionByType(Permission::class);
//		$authorizator->addSetup(['@' . StandardsAuthorizator::class, 'register'], ['@self']);
	}
}

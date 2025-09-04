<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Cli;

use App\Standards\Providers\Unms\Services\UnmsStandardImportRequest;
use App\Standards\Providers\Unms\Services\UnmsStandardImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
	name: 'app:standards:unms:import',
	description: 'Import standard from UNMS by catalogue number.'
)]
final class UnmsStandardImportCommand extends Command
{
	public function __construct(
		private readonly LoggerInterface $logger,
		private readonly UnmsStandardImportService $unmsStandardImportService
	) {
		parent::__construct();
	}

	public function configure(): void
	{
		$this->addArgument('catalogueNumber', InputArgument::OPTIONAL, 'Standard catalogue number.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var array<string,mixed> $args */
		$args = $input->getArguments();

		try {
			$importRequest = new UnmsStandardImportRequest((int)$args['catalogueNumber']);
			$standard = $this->unmsStandardImportService->execute($importRequest);

			$output->writeln('The standard has been imported successfully.');
			$output->writeln(sprintf('The standard ID: %s', $standard->id->toString()));
			$output->writeln(sprintf('The standard code: %s', $standard->code));
			$output->writeln(sprintf('The standard title: %s', $standard->title));

			return Command::SUCCESS;
		} catch (Throwable $e) {
			$this->logger->error('UNMS standard import failed.', ['exception' => $e]);

			$output->writeln('<error>' . $e->getMessage() . '</error>');

			return Command::FAILURE;
		}
	}
}

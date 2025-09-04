<?php

declare(strict_types=1);

namespace App\Standards\Providers\Unms\Cli;

use App\Common\Domain\Exception\LogicException;
use App\Standards\Providers\Unms\Services\UnmsStandardSyncRequest;
use App\Standards\Providers\Unms\Services\UnmsStandardSyncService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(
	name: 'app:standards:unms:sync',
	description: 'Synchronize standards with UNMS.'
)]
final class UnmsStandardSyncCommand extends Command
{
	public function __construct(
		private readonly LoggerInterface $logger,
		private readonly UnmsStandardSyncService $standardSyncService
	) {
		parent::__construct();
	}

	public function configure(): void
	{
		$this->addArgument(
			name: 'skipLoading',
			mode: InputArgument::OPTIONAL,
			description: 'Skip loading standards from UNMS.',
			default: false
		);
		$this->addArgument(
			name: 'skipPostProcessing',
			mode: InputArgument::OPTIONAL,
			description: 'Skip post processing of standards.',
			default: false
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		if (!$output instanceof ConsoleOutputInterface) {
			throw new LogicException('This command accepts only an instance of "ConsoleOutputInterface".');
		}

		/** @var array<string,mixed> $args */
		$args = $input->getArguments();

		$processSection = $output->section();

		$onProgress = function (string $message) use ($processSection): void {
			$processSection->writeln($message);
		};

		$syncRequest = new UnmsStandardSyncRequest(
			onProgress: $onProgress,
			skipLoading: (bool)$args['skipLoading'],
			skipPostProcessing: (bool)$args['skipPostProcessing']
		);

		try {
			$this->standardSyncService->execute($syncRequest);

			return Command::SUCCESS;
		} catch (Throwable $e) {
			$this->logger->error('UNMS standards sync failed.', [
				'exception' => $e
			]);

			$output->writeln('<error>' . $e->getMessage() . '</error>');

			return Command::FAILURE;
		}
	}
}

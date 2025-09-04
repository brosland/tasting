<?php

declare(strict_types=1);

namespace App\Standards\Cli;

use App\Standards\Domain\Ics\IcsCode;
use App\Standards\Domain\Ics\IcsNotFoundException;
use App\Standards\Services\Ics\IcsCreateRequest;
use App\Standards\Services\Ics\IcsCreateService;
use App\Standards\Services\Ics\IcsUpdateRequest;
use App\Standards\Services\Ics\IcsUpdateService;
use Exception;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;

#[AsCommand(
	name: 'app:standards:ics:import',
	description: 'Import ICS codes from JSON.'
)]
final class IcsImportCommand extends Command
{
	public function __construct(
		private readonly IcsCreateService $icsCreateService,
		private readonly IcsUpdateService $icsUpdateService
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setDescription('Import ICS codes from JSON.');
		$this->addArgument('srcDir', InputArgument::REQUIRED, 'ICS source directory.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var array<string,mixed> $args */
		$args = $input->getArguments();

		try {
			$files = glob($args['srcDir'] . '/*.json');

			if ($files === false) {
				throw new RuntimeException('Cannot find files in repo dir.');
			}

			foreach ($files as $file) {
				$fileContents = file_get_contents($file);

				if ($fileContents === false) {
					throw new RuntimeException("Cannot read file '$file'.");
				}

				$data = json_decode($fileContents, true);
				$code = IcsCode::from($data['code']);

				try {
					$updateRequest = new IcsUpdateRequest(
						code: $code,
						title: $data['description'],
						description: $data['descriptionFull'] ?? ''
					);

					$this->icsUpdateService->execute($updateRequest);
				} catch (IcsNotFoundException) {
					$createRequest = new IcsCreateRequest(
						code: $code,
						title: $data['description'],
						description: $data['descriptionFull'] ?? ''
					);

					$this->icsCreateService->execute($createRequest);
				}

				$output->writeln(sprintf('Imported ICS %s from %s.', $code, basename($file)));
			}

			$output->writeln('Done');

			return Command::SUCCESS;
		} catch (Exception $e) {
			Debugger::log($e);

			$output->writeln('<error>' . $e->getMessage() . '</error>');
		}

		return Command::FAILURE;
	}
}

<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\ScryfallSchema;

class ScryfallSchemaUtility extends Command
{

    private ScryfallSchema $scryfallService;
    protected static $defaultName = "scryfall:schema";

    public function __construct(ScryfallSchema $scryfallService)
    {
        $this->scryfallService = $scryfallService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription("get scryfall schema from doc")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(print_r($this->scryfallService->getCardSchemaFromDoc()));
        return Command::SUCCESS;
    }
}

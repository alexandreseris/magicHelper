<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\ScryfallUpdates;

class ScryfallUpdate extends Command
{

    private ScryfallUpdates $scryfallService;
    protected static $defaultName = "scryfall:update";

    public function __construct(ScryfallUpdates $scryfallService)
    {
        $this->scryfallService = $scryfallService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription("Update Data using Scryfall API")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->scryfallService->updateData();
        return Command::SUCCESS;
    }
}

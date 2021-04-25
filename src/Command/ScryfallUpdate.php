<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\Scryfall\Updates;

class ScryfallUpdate extends Command
{

    private Updates $scryfallService;
    protected static $defaultName = "scryfall:update";

    public function __construct(Updates $scryfallService)
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

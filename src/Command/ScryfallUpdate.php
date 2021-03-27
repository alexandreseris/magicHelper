<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\Scryfall;

class ScryfallUpdate extends Command
{

    private Scryfall $scryfallService;
    protected static $defaultName = "scryfall:update";

    public function __construct(Scryfall $scryfallService)
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
        $output->writeln( print_r( $this->scryfallService->updateData("oracle_cards") ) );
        return Command::SUCCESS;
    }
}

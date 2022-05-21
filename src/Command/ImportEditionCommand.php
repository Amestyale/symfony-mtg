<?php

namespace App\Command;

use App\Entity\Edition;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-edition',
    description: 'Add a short description for your command',
)]
class ImportEditionCommand extends Command
{
    private EntityManagerInterface $em;
    private int $counter;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($this::$defaultName);
        $this->em = $em;        
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $json = file_get_contents("./assets/data/SetList.json");


        $objs = json_decode($json, true);
        foreach ($objs["data"] as $key => $obj) {
            $edition = new Edition;
            $edition->setId($obj['code']);
            $edition->setName($obj['name']);
            $edition->setDate(new DateTime($obj['releaseDate']));
            $edition->setIcon("https://c2.scryfall.com/file/scryfall-symbols/sets/".strtolower($obj['keyruneCode']).".svg?1651464000");
            $this->em->persist($edition);
        }
        $this->em->flush();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}

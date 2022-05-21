<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import-card',
    description: 'Add a short description for your command',
)]
class ImportCardCommand extends Command
{
    private $connection;
    
    public function __construct(Connection $connection)
    {
        parent::__construct($this::$defaultName);
        $this->connection = $connection;    
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sql = "LOAD DATA INFILE '".addslashes(realpath('./assets/data/magic-cards.csv'))."' 
        INTO TABLE card 
        CHARACTER SET latin1
        FIELDS TERMINATED BY ';'
        ENCLOSED BY '\"' 
        LINES TERMINATED BY '\n' 
        IGNORE 1 ROWS 
        (@col1,@col2,@col3,@col4,@col5,@col6,@col7,@col8,@col9,@col10,@col11,@col12,@col13,@col14,@col15,@col16,@col17,@col18) 
        set card.id=NULL, card.edition_id = @col12, card.name = @col8, card.cost = @col7, card.description = @col15, card.rarity=@col10, 
        card.image = CONCAT('https://api.scryfall.com/cards/',@col11,'?format=image');";
        
        $statement = $this->connection->prepare($sql);
        $statement->executeQuery();

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}

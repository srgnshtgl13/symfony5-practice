<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{
    protected static $defaultName = 'test-command';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        // 3. Update the value of the private entityManager variable through injection
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $em = $this->entityManager;
        $repo = $em->getRepository("App:User");
        foreach ($repo->findAll() as $key => $value) {
            $schemaName = $value->getSlug();
            // create schema if not exists
            $RAW_QUERY = 'create schema if not exists '.$schemaName;
            $statement = $em->getConnection()->prepare($RAW_QUERY);
            $statement->execute();
            // get schema name from
            $RAW_QUERY_CHECK_SCHEMA_IF_EXISTS = "SELECT schema_name FROM information_schema.schemata WHERE schema_name='".$schemaName."'";
            $statement_for_existing = $em->getConnection()->prepare($RAW_QUERY_CHECK_SCHEMA_IF_EXISTS);
            $statement_for_existing->execute();
            $schemaExistResult = $statement_for_existing->fetch();
            // migrate to private schemas
            if(!empty($schemaExistResult)){
                $io->success($schemaName.' created');
            } else {
                $io->success($schemaName.' => already exist');
            }
        }
        // db purge to public
        $em->getConnection()->prepare('set search_path to public')->execute();

        return 0;
    }
}

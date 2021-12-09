<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Utils\LipsumGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProductsAndImagesCommand extends Command
{
    protected static $defaultName = 'my:data:load';

    /**
     * @var EntityManagerInterface $entityManager
     */
    private EntityManagerInterface $entityManager;

    /**
     * GenerateProductsAndImagesCommand constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generates all the products and images for them')
//            ->addOption('max-images', 'm', InputOption::VALUE_OPTIONAL, 'Limits the number of images generated', 100)
            ->addOption('batch-size', 'b', InputOption::VALUE_OPTIONAL, 'Batch size of generated entities', 20);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $maxResults = $input->getOption('max-images');
        $batch = $input->getOption('batch-size');
        $counter = 0;

        do {
            $product = new Product(LipsumGenerator::getWords(10));

//            $productImage = mt_rand(1, 3) === 3 ? new ProductImage(LipsumGenerator::getBytes()) : null;

            $this->entityManager->persist($product);
            
            if (($counter % $batch) === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        } while ($counter++ <= 500);

        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }
}

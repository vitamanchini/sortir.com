<?php

namespace App\Service;

use App\Entity\Participant;
use App\Entity\Site;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Style\SymfonyStyle;

class CsvParserService
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param ParticipantRepository $participantRepository
     */
    public function __construct(EntityManagerInterface $entityManager,
                                ParticipantRepository  $participantRepository,
                                SiteRepository         $siteRepository)
    {
        $this->entityManager = $entityManager;
        $this->participantRepository = $participantRepository;
        $this->siteRepository = $siteRepository;
    }

    public function parseCsv(string $filepath)
    {
        $csv = Reader::createFromPath($filepath, 'r');
        $csv->setHeaderOffset(0);
        $statement = new Statement();
        $userdata = $statement->process($csv)->jsonSerialize();

        $usersCreated = 0;
        foreach ($userdata as $row) {
//            if(array_key_exists('email', $row) && !empty($row['email'])){
            $user = $this->participantRepository->findOneBy(['email' => $row['email']]);

            $site = $this->siteRepository->find($row['site']);

            if (!$user) {
                $user = new Participant();
                $user->setSite($site)
                    ->setEmail($row['email'])
                    ->setRoles((array)$row['roles'])
                    ->setPassword($row['password'])
                    ->setName($row['name'])
                    ->setSecondName($row['second_name'])
                    ->setTelephone($row['telephone'])
                    ->setActive($row['active'])
                    ->setPseudo($row['pseudo']);
                $this->entityManager->persist($user);
                $usersCreated++;
            }
            if ($usersCreated == 100) {
                break;
            }
        }

        $this->entityManager->flush();
    }

}
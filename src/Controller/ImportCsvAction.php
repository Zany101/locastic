<?php

namespace App\Controller;

use App\Entity\Races;
use App\Dto\ImportCsv;
use App\Service\ImportHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class ImportCsvAction extends AbstractController
{
    public function __invoke(Request $request, ValidatorInterface $validator, EntityManagerInterface $em)
    {
        $file = $request->files->get('upload');
        $title = $request->request->get('title');
        $date = $request->request->get('date');

        $model = new ImportCsv;
        $model->title = $title;
        $model->date = $date;
        $model->file = $file;

        $errors = $validator->validate($model);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $errorsString;
        }

        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()], [new CsvEncoder()]);
        $data = $serializer->deserialize(file_get_contents($model->file), 'App\Entity\Participants[]', 'csv');
        $data = $serializer->normalize($data);

        $importHandler = new ImportHandler($data);
        $result = $importHandler->process();

        $race = new Races;
        $race->setTitle($model->title);
        $race->setDate($model->date);
        $race->setAverageFinishTimeForMediumDistance($importHandler->averageFinishTimeForMediumDistance);
        $race->setAverageFinishTimeForLongDistance($importHandler->averageFinishTimeForLongDistance);
        $race->addParticipants($result);

        $em->persist($race);
        $em->flush();

        return $race;
    }
}

<?php
/**
 * This file handles Race objects as well that of participants, 
 */
namespace App\Service;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;

class ImportHandler
{
    private $data;

    public $resultsMediumDistance;

    public $resultsLongDistance;

    public $averageFinishTimeForMediumDistance;

    public $averageFinishTimeForLongDistance;

    public $processedData;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function filterByParam(array $data, string $criteria, string $parameter)
    {
        $results = array_filter($data, function ($e) use ($criteria, $parameter) {
            if ($e[$criteria] == $parameter) {
                return $e[$criteria] == $parameter;
            } else {
                return false;
            }
        });

        return array_values($results);
    }

    public function averageTime(string|int $start, string|int $last)
    {
        $totaltime = strtotime($start) + strtotime($last);
        $average_time = ($totaltime / 2);

        return date('G:i:s', $average_time);
    }

    public function assingPlacements(array $data)
    {
        $memory = [];
        $counter = 1;

        for ($i = 0; $i < count($data); $i++) {

            $data[$i]['overallPlacement'] = $counter++;
            $category = $data[$i]['ageCategory'];

            // Handle memory to asign placement for placement by age cat
            if (!array_key_exists($category, $memory)) {
                $memory[$category] = 1;
                $data[$i]['ageCategoryPlacement'] = 1;
            } else {
                $memory[$category]++;
                $data[$i]['ageCategoryPlacement'] = $memory[$category];
            }
        }

        return $data;
    }

    public function getTimes($data)
    {
        $times['startTime'] = reset($data)['finishTime'];
        $times['endTime'] = end($data)['finishTime'];

        return $times;
    }

    public function process()
    {
        $data = $this->data;

        # Sort
        $key_values = array_column($data, 'finishTime');
        array_multisort($key_values, SORT_ASC, $data);

        # Split array
        $this->resultsLongDistance = $this->filterByParam($data, 'distance', 'long');
        $this->resultsMediumDistance = $this->filterByParam($data, 'distance', 'medium');

        # averages
        $timeMediumDistance = $this->getTimes($this->resultsMediumDistance);
        $timeLongDistance = $this->getTimes($this->resultsLongDistance);

        $this->averageFinishTimeForMediumDistance = $this->averageTime($timeLongDistance['startTime'], $timeLongDistance['endTime']);
        $this->averageFinishTimeForLongDistance = $this->averageTime($timeMediumDistance['startTime'], $timeMediumDistance['startTime']);

        $this->processedData = $this->assingPlacements($this->resultsLongDistance);

        $mergedData = array_merge($this->processedData, $this->resultsMediumDistance);

        $serializer = new Serializer([new ObjectNormalizer(), new ArrayDenormalizer()], [ new JsonEncoder() ]);

        $json = $serializer->serialize($mergedData, 'json');
        $result = $serializer->deserialize($json, 'App\Entity\Participants[]', 'json');

        return $result;
    }
}

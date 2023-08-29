<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;


final class ImportCsv
{
    #[Assert\NotNull(message: '"Title" is required')]
    public string $title;

    #[Assert\NotNull(message: '"Date" is required')]
    #[Assert\Date(message: "Invalid date format, Y-m-d")]
    public string $date;

    #[Assert\File(mimeTypes: 'text/csv', mimeTypesMessage: "Invalid file type, CSV")]
    #[Assert\NotNull(message: '"File" is required')]
    public $file;
}

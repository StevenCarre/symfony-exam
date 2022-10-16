<?php

namespace App\Entity\Query;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Overblog\GraphQLBundle\Annotation as GQL;

#[GQL\Provider]
class AuthorQuery
{
    public function __construct(
        private readonly AuthorRepository $authorRepository,
    ) {
    }

    /**
     * @return Author[]
     */
    #[GQL\Query(type: "[Author]", name: "Authors")]
    public function AuthorList(): array
    {
        return $this->authorRepository->findBy([]);
    }
}

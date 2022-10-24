<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @var AuthorRepository
     */
    private AuthorRepository $authorRepository;

    /**
     * @var BookRepository
     */
    private BookRepository $bookRepository;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param AuthorRepository $authorRepository
     * @param BookRepository $bookRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        AuthorRepository $authorRepository,
        BookRepository $bookRepository,
        SerializerInterface $serializer
    )
    {
        $this->authorRepository = $authorRepository;
        $this->bookRepository = $bookRepository;
        $this->serializer = $serializer;
    }

    /**
     * lists authors searching by name or first name
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/author/list', name: 'list-authors', methods: ['GET'])]
    public function listAuthors(Request $request): JsonResponse
    {
        $searchTerm = $request->query->get('searchTerm');
        $authors = $this->authorRepository->findBySearchTerm($searchTerm);
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['Author', 'Author_detail', 'Book'])
            ->toArray();

        $result = ['Authors' => []];
        foreach ($authors as $author) {
            $result['Authors'][] = [
                'Author' => $author,
                'Number Of Books' => $this->bookRepository->countBooks($author)
            ];
        }

        return $this->json($result, 200, ['Content-Type' => 'application/json'], $context);
    }

    /**
     * creates an author
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/author/create', name: 'create-author', methods: ['POST'], format: 'json')]
    public function createAuthor(Request $request): JsonResponse
    {
         if ($request->headers->get('content-type') != 'application/json') {
             throw new BadRequestException('Content type header must be application/json');
         }
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['Author', 'Author_detail', 'Book'])
            ->toArray();

        $json = $request->getContent();
        $author = $this->serializer->deserialize($json, Author::class, 'json');

        $this->authorRepository->save($author, true);

        return $this->json($author, 200, ['Content-Type' => 'application/json'], $context);
    }
}
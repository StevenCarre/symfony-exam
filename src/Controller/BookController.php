<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class BookController extends AbstractController
{
    /**
     * @var BookRepository
     */
    private BookRepository $bookRepository;

    /**
     * @param BookRepository $bookRepository
     */
    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * return all book titles in json format
     */
    #[Route('/books/list', name: 'list-of-my-books', methods: ['POST'], format: 'json')]
    public function book(): JsonResponse // method had no return type
    {
        $books = $this->bookRepository->findAll();

        // we will use a serialization context to get only the titles of the books. see annotations in Book Entity class
        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['Book'])
            ->toArray();

        return $this->json($books, 200, ['Content-Type' => 'application/json'], $context);
    }

    /**
     * Traverse all books and add suffix on names
     */
    #[Route('/books/add-suffix/{suffix}', name: 'add-suffix-on-my-books', methods: ['GET'], format: 'json')]
    public function addSuffix(string $suffix): JsonResponse
    {
        $books = $this->bookRepository->findAll();

        foreach ($books as $key => $book) {
            $flush = $key === count($books) - 1; // only flush on the last iteration for better performance
            $book->setTitle($book->getTitle() . " - $suffix");
            $this->bookRepository->save($book, $flush);
        }

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(['Book'])
            ->toArray();

        return $this->json([
            'data' => 'ok',
            'books' => $books
            ],
            200,
            ['Content-Type' => 'application/json'],
            $context);
    }

    // old version with comments
//    #[Route('/books/list', name: 'list-of-my-books', methods: ['POST'], format: 'json')]
//    public function book()
//    {
//        $book = $this->container->get('doctrine.orm.default_entity_manager')->getRepository("App\Entity\Book")->findBy(['id' => 1]);
//        // since Symfony 5, we cannot access the container like this. it's more straightforward to inject the repository in constructor or method arguments
//          + variable misnamed (is potentially several books)
//          + we want  to list all books, not just the first one (find by id =1)

//        $template = $this->container->get('twig')->load('book/index.html.twig');
//        // looks like Symfony 2 gone wrong

//        return $template->render([
//            'return' => json_encode([
//                'data' => json_encode($book[0]['name'])
//            ]),
//        ]);
            // we don't want to render a template here, as annotation indicates a json return
//    }
//
//    /**
//     * parcour all books and add sufix on name
// better avoid typos in comments when possible
//     */
//    #[Route('/books/add-sufix', name: 'add-sufix-on-my-books', methods: ['GET'], format: 'json')]
// typos in suffix
//    public function addSufix(string $suffix)
// if we want to use $suffix as method argument, it should be in the url of the route
//    {
//        $books = $this->container->get('doctrine.orm.default_entity_manager')->getRepository("App\Entity\Book")->findBy([]);
// same as above, can't access container this way
//        foreach ($books as $book) {
//            $book->name .= ' - Sufix'; // will throw an undefined property error
//            $this->container->get('doctrine.orm.default_entity_manager')->persist($book);
//            $this->container->get('doctrine.orm.default_entity_manager')->flush();
            // the repository classes implement ad hoc methods to do this
//        }
//
//
//        $template = $this->container->get('twig')->load('book/index.html.twig');
    // we don't need twig, we want a json response + wrongly accessing container
//
//        return $template->render([
//            'return' => json_encode([
//                'data' => json_encode('ok'),
//                'books' => json_encode($books)
//            ]),
//        ]);
// same as first method
//    }
}

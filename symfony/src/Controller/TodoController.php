<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Todo;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TodoController extends AbstractController
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var \Symfony\Component\Serializer\Serializer
     */
    private Serializer $serializer;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $encoders = [new JsonEncoder()];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/todos", name="post_todo", methods={"POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function store(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        /**
         * @var Todo $todo
         */
        $todo = $this->serializer->deserialize(
            $request->getContent(),
            Todo::class,
            'json'
        );

        $todo->setCategory($this->getCategory($request));
        $todo->setDone(false);

        $entityManager->persist($todo);
        $entityManager->flush();

        return $this->getResponse($todo);
    }

    /**
     * @Route("/todos", name="get_todos", methods={"GET"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $all = $this->getDoctrine()
            ->getRepository(Todo::class)
            ->findBy(['category' => $this->getCategory($request)]);

        return $this->getResponse($all);
    }

    /**
     * @Route("/todos/{id}", name="delete_todo", methods={"DELETE"})
     * @param \App\Entity\Todo $todo
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Todo $todo)
    {
        $this->entityManager->remove($todo);
        $this->entityManager->flush();
        return new Response();
    }

    /**
     * @Route("/todos/{id}", name="update_todo", methods={"PATCH"})
     * @param \App\Entity\Todo $todo
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Todo $todo, Request $request)
    {
        $todo->setDone($request->get('done', $todo->getDone()));
        $todo->setTitle($request->get('title', $todo->getTitle()));
        $this->entityManager->persist($todo);

        return $this->getResponse($todo);
    }

    private function getResponse($object)
    {
        return new Response($this->serializer->serialize($object, 'json', ['groups' => 'api']));
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \App\Entity\Category
     */
    protected function getCategory(Request $request): \App\Entity\Category
    {
        /**
         * @var Category $category
         */
        $category = $this->entityManager->find(Category::class, $request->get('category_id'));
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }

        return $category;
    }
}

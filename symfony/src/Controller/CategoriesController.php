<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CategoriesController extends AbstractController
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
     * @Route("/categories", name="categories_create", methods={"POST"})
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return string
     */
    public function store(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->serializer->deserialize($request->getContent(), Category::class, 'json');
        $entityManager->persist($category);
        $entityManager->flush();

        return $this->getResponse($category);
    }

    private function getResponse($object)
    {
        return new Response($this->serializer->serialize($object, 'json', ['groups' => 'api']));
    }

    /**
     * @Route("/categories", name="categories_index", methods={"GET"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $all = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->getResponse($all);
    }

    /**
     * @Route("/categories/{id}", name="categories_delete", methods={"DELETE"})
     * @param \App\Entity\Category $category
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete(Category $category)
    {
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return new Response();
    }

    /**
     * @Route("/categories/{id}", name="categories_update", methods={"PATCH"}, requirements={"id":"\d+"})
     * @param \App\Entity\Category $category
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function update(Category $category, Request $request)
    {
        $this->serializer->deserialize(
            $request->getContent(),
            Category::class,
            'json',
            ['object_to_populate' => $category]
        );
        $this->entityManager->persist($category);

        return $this->getResponse($category);
    }
}

<?php

namespace App\UI\Http\Web\Controller\Post;

use App\Application\UseCase\Command\Post\Create\CreatePostCommand;
use App\Application\UseCase\Command\Post\Create\CreatePostUseCase;
use App\Domain\Post\Exception\InvalidPostDataException;
use App\Domain\Shared\Http\FlashMessageServiceInterface;
use App\Domain\Shared\Http\HttpResponseFactoryInterface;
use App\Domain\Shared\Http\HttpResponseInterface;
use App\UI\Http\Web\Form\Post\PostType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts/create", name="app.post.create")
 */
class CreatePostController extends AbstractController
{
    private HttpResponseFactoryInterface $responseFactory;
    private FlashMessageServiceInterface $flashMessageService;

    public function __construct(
        HttpResponseFactoryInterface $responseFactory,
        FlashMessageServiceInterface $flashMessageService
    ) {
        $this->responseFactory = $responseFactory;
        $this->flashMessageService = $flashMessageService;
    }

    /**
     * @param Request $request
     * @param CreatePostUseCase $createPostUseCase
     *
     * @return HttpResponseInterface
     */
    public function __invoke(Request $request, CreatePostUseCase $createPostUseCase): HttpResponseInterface
    {
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            /** @var string $title */
            $title = $form->get('title')->getData();

            /** @var string $content */
            $content = $form->get('content')->getData();

            /** @var DateTime|null $publishedAt */
            $publishedAt = $form->get('publishedAt')->getData();

            $createPostCommand = new CreatePostCommand(
                $title, $content, $publishedAt
            );

            try {
                $post = $createPostUseCase->create($createPostCommand);

                $this->flashMessageService->add('success', "{$post->getPost()->getTitle()} created.");

                return $this->responseFactory->redirect('app.post.create');
            } catch (InvalidPostDataException $dataException) {
                $this->flashMessageService->add('error', $dataException->getMessage());
            }
        }

        return $this->responseFactory->render('post/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

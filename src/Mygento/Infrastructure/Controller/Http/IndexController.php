<?php

namespace App\Mygento\Infrastructure\Controller\Http;

use App\Mygento\Application\Security\User\IdentityUser;
use App\Mygento\Domain\Model\User\IdentityUser\IdentityUserInterface;
use App\Mygento\Domain\UseCase\News\DTO\AddUserLikeDTO;
use App\Mygento\Domain\UseCase\News\DTO\RemoveUserLikeDTO;
use App\Mygento\Domain\UseCase\News\DTO\UserLikeDTO;
use App\Mygento\Domain\UseCase\UseCase;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class IndexController extends AbstractController
{
    private Security $security;

    private UseCase $useCase;

    public function __construct(
        Security $security,
        UseCase $useCase
    ) {
        $this->security = $security;
        $this->useCase = $useCase;
    }

    /**
     * @Route("/", name="app_index")
     */
    public function index(): Response
    {
        $newsArray = $this->useCase->getAllNewsWithUsersLikes();

        return $this->render(
            '@Mygento/index/index.html.twig',
            [
                'newsArray' => $newsArray
            ]
        );
    }

    /**
     * @Route("/news/{newsId}/like", name="toggle_user_like", methods={"POST"})
     */
    public function toggleLike(Request $request, string $newsId): Response
    {
        /** @var IdentityUserInterface $user */
        $user = $this->security->getUser();
        $userId = $user->getDomainUserId();

        $userLikeDTO = new UserLikeDTO();
        $userLikeDTO->userId = $userId;
        $userLikeDTO->newsId = $newsId;

        try {
            $isLiked = $this->useCase->toggleUserLike($userLikeDTO);
            if ($isLiked) {
                $message = 'Лайк был успешно добавлен!';
            } else {
                $message = 'Лайк был успешно удалён!';
            }
        } catch (Exception $exception) {
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse($exception->getMessage(), 400);
            } else {
                $this->addFlash('error', $exception->getMessage());
                return $this->redirectToRoute('app_index');
            }
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($message);
        } else {
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_index');
        }
    }
}
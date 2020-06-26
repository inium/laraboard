<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\BoardTrait;
use Inium\Laraboard\App\Board\PostTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Middleware\PostWriteMiddleware;

class PostWriteController extends Controller
{
    use BoardTrait, PostTrait, RenderTemplateTrait;

    /**
     * Form validation rules
     *
     * @var array
     */
    private $rules = [
        'notice' => 'boolean',
        'subject' => 'required',
        'content' => 'required'
    ];

    /**
     * Form validation messages
     *
     * @var array
     */
    private $messages = [
        'subject.required' => '제목을 입력해주세요.',
        'content.required' => '내용을 입력해주세요.'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 게시글 쓰기 미들웨어
        $this->middleware(PostWriteMiddleware::class);
    }

    /**
     * 게시글 쓰기 페이지
     * 
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}/write
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * -------------------------------------------------------------------------
     */
    public function view(Request $request, string $boardName)
    {
        $params = [
            'board' => $this->getBoardByName($boardName),
            'roles' => BoardUserRoles::roles($boardName)
        ];

        return $this->render('postWrite', $params);
    }

    /**
     * 게시글 저장
     * 
     * -------------------------------------------------------------------------
     * POST [/{$prefix}]/board/{boardName}/write
     * 
     * subject=lorem
     * content=<p>dolor</p>
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * 
     * Post params
     * @param string subject 게시글 제목
     * @param string content 게시글 내용 (HTML)
     * -------------------------------------------------------------------------
     */
    public function submit(Request $request, string $boardName)
    {
        $validator = Validator::make($request->all(),
                                     $this->rules,
                                     $this->messages);

        // Validation 실패
        if ($validator->fails()) {
            return redirect()->route('board.post.write.view', [
                            'boardName' => $boardName
                        ])
                        ->withErrors($validator)
                        ->withInput();
        }

        // 공지글 여부
        $notice = is_null($request->notice) ? false : true;

        $submitPostId = $this->submitPost($request->server('HTTP_USER_AGENT'),
                                         $boardName,
                                         $request->subject,
                                         $request->content,
                                         $notice);

        // 게시글 저장에 성공한 경우
        if ($submitPostId) {
            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'id' => $submitPostId
                        ]);
        }
        // 게시글 저장에 실패한 경우
        else {
            $errorMessage = '게시글 저장에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.write.view', [
                            'boardName' => $boardName
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }
}

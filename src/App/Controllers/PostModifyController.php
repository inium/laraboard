<?php

namespace Inium\Laraboard\App\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inium\Laraboard\App\Board\BoardUserRoles;
use Inium\Laraboard\App\Board\PostTrait;
use Inium\Laraboard\App\Board\RenderTemplateTrait;
use Inium\Laraboard\App\Middleware\PostModifyMiddleware;

class PostModifyController extends Controller
{
    use PostTrait, RenderTemplateTrait;

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
        $this->middleware(PostModifyMiddleware::class);
    }

    /**
     * 게시글 수정 페이지
     * 
     * -------------------------------------------------------------------------
     * GET [/{$prefix}]/board/{boardName}/modify/{id}
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer id        게시글 ID
     * -------------------------------------------------------------------------
     */
    public function view(Request $request, string $boardName, int $id)
    {
        $params = [
            'post' => $this->getPost($boardName, $id),
            'roles' => BoardUserRoles::roles($boardName)
        ];

        return $this->render('postModify', $params);
    }

    /**
     * 게시글 수정
     * 
     * -------------------------------------------------------------------------
     * PUT [/{$prefix}]/board/{boardName}/modify/{id}
     * 
     * subject=lorem
     * content=<p>ipsum</p>
     * 
     * Route params
     * @param string $prefix    Route Prefix. 환경설정(laraboard.php) 참조.
     * @param string boardName  게시판 영문 이름.
     * @param integer id        게시글 ID
     * 
     * Put params
     * @param string subject 게시글 제목
     * @param string content 게시글 내용 (HTML)
     * -------------------------------------------------------------------------
     */
    public function put(Request $request, string $boardName, int $id)
    {
        // $post = $this->getPost($boardName, $id);
        $validator = Validator::make($request->all(),
                                     $this->rules,
                                     $this->messages);

        // Validation 실패
        if ($validator->fails()) {
            return redirect()->route('board.post.modify.view', [
                            'boardName' => $boardName,
                            'id' => $id
                        ])
                        ->withErrors($validator)
                        ->withInput();
        }

        // 공지글 여부
        $notice = is_null($request->notice) ? false : true;

        $updated = $this->putPost($request->server('HTTP_USER_AGENT'),
                                  $boardName,
                                  $id,
                                  $request->subject,
                                  $request->content,
                                  $notice);

        // 게시글 저장에 성공한 경우
        if ($updated) {
            // 성공 메시지 추가
            Session::flash('message', '게시글 수정이 완료되었습니다.');
            Session::flash('alert-class', 'alert-success');

            return redirect()->route('board.post.view', [
                            'boardName' => $boardName,
                            'id' => $id
                        ]);
        }
        // 게시글 저장에 실패한 경우
        else {
            $errorMessage = '게시글 수정에 실패하였습니다. 다시 시도해주세요.';
            return redirect()->route('board.post.modify.view', [
                            'boardName' => $boardName,
                            'id' => $id
                        ])
                        ->withErrors(array($errorMessage))
                        ->withInput();
        }
    }
}

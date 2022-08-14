# laraboard

Laravel 게시판 스캐폴딩(Scaffolding) 패키지 입니다.

API 형태로 Laravel 9.x / PHP 8.x 기반으로 제작되었습니다. 게시판 게시글, 2 Depth 댓글을 지원하며 로그인, 회원가입은 Laravel에서 기본으로 제공하는 인증 스캐폴딩(Auth Scaffolding)을 이용합니다.

## 구성

본 게시판 패키지는 아래의 항목으로 구성되어 있습니다.

| 항목 | 내용 | 비고 |
| ---- | ---- | ---- |
| 게시판<br>(board) | 게시판 게시글과 댓글 작성시 부여할 포인트 설정<br> 페이지당 보여질 게시글 수 / 댓글 수 설정 | 게시판 테이블에서 직접 설정 |
| 게시글<br>(post) | 게시판별 게시글 목록, 검색, 조회, 추가, 수정, 삭제<br> 게시글 제목 / 본문 검색<br> 게시글 조회 시 조회수 1 증가<br> 게시글 추가 시 게시판에서 설정된 포인트 부여<br> 게시글 추가, 수정, 삭제 시 작성자 정보 저장 (Optional)<br> 게시글 추가, 수정, 삭제 시 작성자 인증 확인<br> 게시글에 댓글이 존재할 시 삭제 불가 | 작성자 정보는 암호화된 Ip Address, User Agent와 User Agent 기반으로 분석한 사용자 정보(Device, OS, Browser) 저장 (Optional)<br> 게시글 추가 시 검색용으로 [Strip tag](https://www.php.net/manual/en/function.strip-tags.php)된 게시글 본문 별도 저장<br> 게시글 삭제 시 [Soft Delete](https://laravel.kr/docs/9.x/eloquent#%EC%86%8C%ED%94%84%ED%8A%B8%20%EC%82%AD%EC%A0%9C%ED%95%98%EA%B8%B0) 적용 |
| 댓글<br>(comment) | 게시글 댓글 목록, 검색, 조회, 추가, 수정, 삭제<br> 댓글 본문 검색<br> 댓글 추가 시 게시판에 설정된 포인트 부여<br> 댓글 추가, 수정, 삭제 시 작성자 정보 저장 (Optional)<br> 댓글 추가, 수정, 삭제 시 작성자 인증 확인<br> 댓글에 댓글 (대댓글) 존재 시 해당 댓글 삭제 불가 | 작성자 정보는 암호화된 Ip Address, User Agent와 User Agent 기반으로 분석한 사용자 정보(Device, OS, Browser) 저장 (Optional)<br> 댓글 추가 시 검색용으로 [Strip tag](https://www.php.net/manual/en/function.strip-tags.php)된 게시글 본문 별도 저장<br> 댓글 삭제 시 [Soft Delete](https://laravel.kr/docs/9.x/eloquent#%EC%86%8C%ED%94%84%ED%8A%B8%20%EC%82%AD%EC%A0%9C%ED%95%98%EA%B8%B0) 적용 |
| 데이터베이스<br>(database) | 게시판, 게시글, 댓글 테이블(migration) <br> 게시판, 게시글 댓글 테스트 데이터 (factory, seeder) <br>| 게시글 200개 (일반글 )+ 5개(공지사항) 생성 <br> 댓글 100개 + 100개 댓글별 1~8개 사이의 자식 댓글 생성 |

### 사용자 정보 저장

`config/laraboard.php`의 `collect_user_info` 항목을 true로 설정할 경우 아래의 정보를 게시글 및 댓글 작성 시 같이 저장합니다.

| 항목 | 내용 | 비고 |
| ---- | ---- | ---- |
| IP | 접속한 사용자의 IP Address | 암호화 하여 저장 |
| User Agent | 접속한 사용자의 User Agent 문자열 | 암호화 하여 저장 |
| Device Type | 접속한 사용자가 사용한 기기 형태 | desktop, tablet, mobile, others 중 1<br> [Agent.php](src/Support/Detect/Agent.php) 참조|
| OS Name | 접속한 사용자의 OS 이름 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null |
| OS Version | 접속한 사용자의 OS 버전 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null  |
| Browser Name | 접속한 사용자의 Browser 이름 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null  |
| Browser Name | 접속한 사용자의 Browser 버전 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null  |

### 사용자 정보 인증

> 주의: Laravel의 HTTP 기본 인증은 email:password 문자열을 base64 인코딩하여 사용하기 때문에 보안에 취약하니 본 패키지 사용 시 반드시 변경하여 사용하시는 것을 권장합니다.

본 패키지는 구현의 편의를 위해 [HTTP 기본 인증](https://laravel.kr/docs/9.x/authentication#HTTP%20%EA%B8%B0%EB%B3%B8%20%EC%9D%B8%EC%A6%9D)을 이용하였습니다.

사용자 인증이 적용되는 범위는 아래와 같습니다.

| 항목 | 인증범위 | 비고 |
| ---- | ---- | ---- |
| 게시글<br>(post) | 등록(POST), 수정(PUT), 삭제(DELETE) |  |
| 댓글<br>(comment) | 등록(POST), 수정(PUT), 삭제(DELETE) |  |

### Strip Tag: 게시글 / 댓글 저장

- 게시글, 댓글 본문 저장 시 strip tag를 적용하며 XSS Protection을 적용하였습니다.
- 허용할 tag는 [`config/laraboad.php`](src/Laraboard/config/laraboard.php)의 `allow_post_content_tags`, `allow_comment_content_tags` 설정할 수 있습니다.

### API 명세

본 패키지의 게시글, 댓글에 대한 명세는 [게시글 API](rest.comment.example.http), [댓글 API](rest.comment.example.http) 참조 바랍니다.

## 의존성

본 게시판 패키지는 Laravel 9.x / PHP 8.x 에서 구현 및 테스트 하였습니다.

또한 본 게시판 패키지에서는 아래의 패키지들을 추가로 사용합니다.

| 항목 | 패키지 | 버전 | 설명 | 비고 |
| ---- | ----- | ---- | ---- | ----- |
| Agent Detect | [jenssegers/agent](https://packagist.org/packages/jenssegers/agent) | 사용자 IP Address, User Agent, OS 이름/버전, 접속 Browser 이름/버전 분석 | 3.0@dev | `composer` 설치 (본 패키지 설치 시 자동으로 같이 설치됨). |

## 사용방법

TBD 

## License

MIT
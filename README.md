# laraboard

Laravel 게시판 스캐폴딩(Scaffolding) 패키지 입니다.

API 형태로 사용하기 위해 Laravel 9.x / PHP 8.x 기반으로 제작하였습니다. 게시판 게시글, 2 Depth 댓글을 지원하며 회원 정보는 Laravel에서 기본으로 제공하는 인증 스캐폴딩(Auth Scaffolding)인 users를 이용합니다.

- API 형태로 제작하였기 때문에 view 파일은 존재하지 않습니다.

## 구성

본 게시판 패키지는 아래의 항목으로 구성되어 있습니다.

| 항목 | 내용 | 비고 |
| --- | --- | --- |
| 게시판<br>(board) | - 게시판 게시글과 댓글 작성시 부여할 포인트 설정<br> - 페이지당 보여질 게시글 수 / 댓글 수 설정 | 게시판 테이블에서 직접 설정 |
| 게시글<br>(post) | - 게시판별 게시글 목록, 검색, 조회, 추가, 수정, 삭제<br> - 게시글 제목 / 본문 검색<br> - 게시글 조회 시 조회수 1 증가<br> - 게시글 추가 시 게시판에서 설정된 포인트 부여<br> - 게시글 추가, 수정, 삭제 시 작성자 정보 저장 (Optional)<br> - 게시글 추가, 수정, 삭제 시 작성자 인증 확인<br> - 게시글에 댓글이 존재할 시 삭제 불가 | - 게시글 추가 시 검색용으로 [Strip tag](https://www.php.net/manual/en/function.strip-tags.php)된 게시글 본문 별도 저장<br> - 게시글 삭제 시 [Soft Delete](https://laravel.kr/docs/9.x/eloquent#%EC%86%8C%ED%94%84%ED%8A%B8%20%EC%82%AD%EC%A0%9C%ED%95%98%EA%B8%B0) 적용 |
| 댓글<br>(comment) | - 게시글 댓글 목록, 검색, 조회, 추가, 수정, 삭제<br> - 댓글 본문 검색<br> - 댓글 추가 시 게시판에 설정된 포인트 부여<br> - 댓글 추가, 수정, 삭제 시 작성자 정보 저장 (Optional)<br> - 댓글 추가, 수정, 삭제 시 작성자 인증 확인<br> - 댓글에 댓글 (대댓글) 존재 시 해당 댓글 삭제 불가 | - 댓글 추가 시 검색용으로 [Strip tag](https://www.php.net/manual/en/function.strip-tags.php)된 게시글 본문 별도 저장<br> - 댓글 삭제 시 [Soft Delete](https://laravel.kr/docs/9.x/eloquent#%EC%86%8C%ED%94%84%ED%8A%B8%20%EC%82%AD%EC%A0%9C%ED%95%98%EA%B8%B0) 적용 |
| 데이터베이스<br>(database) | - 게시판, 게시글, 댓글 테이블(migration)<br> - 게시판, 게시글 댓글 테스트 데이터 (factory, seeder) <br>| - 게시글 200개 (일반글 )+ 5개(공지사항) 생성 <br> - 댓글 100개 + 100개 댓글별 1~8개 사이의 자식 댓글 생성 |

### 작성자 정보 저장 (Optional)

[`config/laraboad.php`](src/Laraboard/config/laraboard.php)의 `collect_user_info` 항목을 true로 설정할 경우 아래의 정보를 게시글 및 댓글 작성 시 같이 저장합니다.

| 항목 | 내용 | 비고 |
| --- | --- | --- |
| IP | 접속한 사용자의 IP Address | 암호화 하여 저장 |
| User Agent | 접속한 사용자의 User Agent 문자열 | 암호화 하여 저장 |
| Device Type | 접속한 사용자가 사용한 기기 형태 | desktop, tablet, mobile, others 중 1<br> [Agent.php](src/Support/Detect/Agent.php) 참조|
| OS Name | 접속한 사용자의 OS 이름 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null |
| OS Version | 접속한 사용자의 OS 버전 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null  |
| Browser Name | 접속한 사용자의 Browser 이름 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null  |
| Browser Name | 접속한 사용자의 Browser 버전 | [Agent.php](src/Support/Detect/Agent.php) 참조 / 확인불가 시 null  |

### 사용자 정보 인증

> **주의: Laravel의 HTTP 기본 인증은 email:password 문자열을 base64 인코딩하여 사용하기 때문에 보안에 취약하니 본 패키지 사용 시 반드시 변경하여 사용하시는 것을 권장합니다.**

본 패키지는 구현의 편의를 위해 [HTTP 기본 인증 (Basic Auth)](https://laravel.kr/docs/9.x/authentication#HTTP%20%EA%B8%B0%EB%B3%B8%20%EC%9D%B8%EC%A6%9D)을 이용하였습니다.

사용자 인증이 적용되는 범위는 아래와 같습니다.

| 항목 | 인증범위 | 비고 |
| --- | --- | --- |
| 게시글<br>(post) | 등록(POST), 수정(PUT), 삭제(DELETE) | HTTP 기본 인증 (Basic Auth) 적용 |
| 댓글<br>(comment) | 등록(POST), 수정(PUT), 삭제(DELETE) | HTTP 기본 인증 (Basic Auth) 적용 |

### Strip Tag: 게시글 / 댓글 저장

게시글, 댓글 본문 저장 시 strip tag를 적용하며 XSS Protection을 적용하였습니다.

허용할 tag는 [`config/laraboad.php`](src/Laraboard/config/laraboard.php)의 `allow_post_content_tags`, `allow_comment_content_tags` 에서 설정할 수 있습니다.

## Dependencies

본 패키지는 아래의 의존성을 가지고 개발되었습니다.

| 항목 | 패키지 | 버전 | 설명 | 비고 |
| --- | --- | --- | --- | --- |
| Framework | Laravel | 9.x | - | - |
| Language | PHP | 8.x | - | - |
| External Pcakge | [jenssegers/agent](https://packagist.org/packages/jenssegers/agent) | 3.0@dev | 사용자 IP Address, User Agent, OS 이름/버전, 접속 Browser 이름/버전 분석 | `composer` 설치 (본 패키지 설치 시 자동으로 같이 설치) |

## 사용방법

패키지 사용 방법은 아래와 같습니다.
### 1. Package install

아래와 같이 Laravel 9.x가 설치된 프로젝트 디렉터리 내에서 `composer` 명령어를 이용해 설치합니다.

```bash
composer require inium/laraboard
```

### 2. Publish files & Append routes

아래 명령어를 이용해 Laraboard의 파일들을 Publish 하고 route를 routes/api.php에 append 합니다

```bash
php artisan laraboard:publish
```

명령어를 실행하면 아래의 경로에 Laraboard 파일들을 생성합니다.

| 항목 | Path | 설명 | 비고 |
| --- | --- | --- | ---|
| Controller | App\Http\Controllers\Laraboard | Laraboard 컨트롤러||
| Models | App\Http\Models\Laraboard | Laraboard 모델 | Publish |
| Requests | App\Http\Requests\Laraboard | Laraboard Request <br> - Validation 수행 | Publish |
| Config | config/laraboard.php | Laraboard 환경설정 파일 | Publish |
| Database <br> Migrations | database/migrations/laraboard | Laraboard 데이터베이스 테이블 정의 | Publish |
| Database <br> Factories | Database\Factories | Laraboard  데이터베이스 팩토리 | Publish |
| Database <br> Seeders | Database\Seeders\Laraboard | Laraboard 데이터베이스 Seed | Publish |
| Route | routes/api.php | Laraboard API route를 routes/api.php에 append | Append |

- 비고 > Publish: 패키지 내 정의된 Laraboard 코드를 프로젝트에 배포합니다.
- 비고 > Append: 패키지 내 정의된 Laraboard 코드를 다른 코드에 붙입니다.

Laraboard API Route는 중복 복사 방지를 위해 랜덤한 문자열을 주석으로 추가하여 routes/api.php에 붙여넣습니다. 랜덤 문자열을 삭제 후 `php artisan laraboard:publish` 명령을 실행하면 Laraboard API Route가 routes/api.php에 중복되어 붙여넣어집니다. 사용되는 랜덤 문자열은 아래와 같습니다.

```php
/*
|--------------------------------------------------------------------------
| Laraboard API Routes
|
| DO NOT DELETE BELOW RANDOM STRING FOR AVOID DUPLICATION
| SBiEoIwKajrdqngeEjZQz1RhAGS4mLbZ5hm5xNivTR5BWLHNjh
|--------------------------------------------------------------------------
*/
```

### 3. Database migration

아래 명령어를 이용해 Laraboard 테이블 정보를 migration 합니다.

```bash
php artisan migrate --path=database/migrations/laraboard
```

### 4. (Optional) 테스트 데이터 생성

테스트를 위한 데이터가 필요할 경우 아래 명령어를 이용해 테스트 데이터를 Laraboard 테이블에 추가할 수 있습니다.

```bash
php artisan db:seed --class="Database\\Seeders\\Laraboard\\LaraboardSeeder" 
```

* 위 명령어 실행 시 많은 데이터를 추가하기 때문에 오랜 시간이 소요됩니다.

## 기타

### Timezone

본 게시판 패키지의 Timezone은 Laravel 프로젝트의 설정파일인 `config/app.php`에 지정된 Timezone을 이용합니다. 기본 Timezone은 UTC 입니다.

### 파일 업로드

본 게시판 패키지는 별도의 파일 업로드 기능이 구현되어 있지 않습니다.

### 관리

본 게시판 패키지의 관리 페이지는 구현되어 있지 않습니다.

## API 명세

본 패키지의 게시글, 댓글 API에 대한 사용 방법은 아래 내용을 참조 바랍니다.

- [게시글 API 명세](rest.comment.example.http)
- [댓글 API 명세](rest.comment.example.http)

## License

MIT

# laraboard

Laravel 게시판 패키지 입니다.

커뮤니티 등의 게시판 형태로 Laravel 7.x 를 기반으로 제작되었습니다. 게시판 게시글, 2 Depth 댓글을 지원하며 로그인, 회원가입은 Laravel의 인증 스캐폴딩(Auth Scaffolding)을 이용합니다.

## 구성

본 게시판 패키지는 아래의 항목으로 구성되어 있습니다.

| 항목                            | 내용                                                         | 비고                                                         |
| ------------------------------- | ------------------------------------------------------------ | ------------------------------------------------------------ |
| 게시판                          | 게시판별 게시글 / 댓글 작성시 부여할 포인트 설정<br />게시판별 사용자의 게시글 / 댓글의 읽기, 쓰기 역할(Role) 설정<br />페이지당 보여질 게시글 수 / 댓글 수 설정 | 데이터베이스에서 직접 설정                                   |
| 게시글 / 댓글                   | 게시글 / 댓글의 목록, 글 읽기, 쓰기, 수정, 삭제<br />게시글 / 댓글 작성 시 게시판에 설정된 포인트 추가 | 삭제 제한<br />- [Soft delete](https://laravel.kr/docs/7.x/eloquent#%EC%86%8C%ED%94%84%ED%8A%B8%20%EC%82%AD%EC%A0%9C%ED%95%98%EA%B8%B0) 적용<br />- 게시글에 댓글 있을시 삭제 불가<br />- 댓글에 답글(대댓글) 있을 시 삭제 불가 |
| 콘텐츠 검색                     | 게시판별 게시글 제목 / 내용, 댓글 내용 통합 검색             |                                                              |
| 게시판 사용자 정보 저장         | 회원가입 시 게시판 사용자 정보 저장                          | Laravel의 인증 스캐폴딩의 User 수정하지 않음                 |
| 사용자 역할(Role)               | 게시판 사용자 역할(Role) 정보 저장<br />- 사용자 역할 정보는 게시판별 게시글 읽기/쓰기, 댓글 읽기/쓰기에 사용 | 데이터베이스에서 직접 설정                                   |
| 작성자 통계정보 수집 (Optional) | 게시글 / 댓글 작성자의 IP Address, User Agent 수집. <br />- User Agent를 분석하여 접속기기, 접속한 OS 이름/버전, 접속한 Browser 이름/버전 정보 저장<br />- 기본설정은 수집하지 않음 | 통계정보 수집 시 IP Address, User Agent 정보는 암호화 하여 저장 |
| Test Data                       | 테스트용 Database Factory, Seeder 제공                       | 게시판 1개(자유게시판), 게시글 50개, 댓글 300개              |
## 의존성

본 게시판 패키지는 Laravel 7.x와 MySQL:5.6 (MariaDB:10.4.13) 에서 구현 및 테스트 되었습니다.

또한 본 게시판 패키지에서는 아래의 패키지들을 추가로 사용합니다.

| 항목             | 패키지                                                       | 설명                                                         | 비고                                                      |
| ---------------- | ------------------------------------------------------------ | ------------------------------------------------------------ | --------------------------------------------------------- |
| Frontend Library | [Bootstrap](https://getbootstrap.com/)                       | 웹 사이트 Layout에 사용                                      | CDN 추가하여 사용                                         |
| WYSIWYG          | [Quill.js](https://quilljs.com)                              | 게시판, 댓글 작성 및 수정 시 사용                            | CDN 추가하여 사용                                         |
| Agent Detect     | [jenssegers/agent](https://packagist.org/packages/jenssegers/agent) | 사용자 IP Address, User Agent, OS 이름/버전, 접속 Browser 이름/버전 분석 | `composer` 설치 (본 패키지 설치 시 자동으로 같이 설치됨). |

사용한 Resource는 아래와 같습니다.

| 항목      | 설명                                                         | 비고                                                         |
| --------- | ------------------------------------------------------------ | ------------------------------------------------------------ |
| Thumbnail | [iconfinder](https://www.iconfinder.com/icons/4696674/account_avatar_male_people_person_profile_user_icon)의 이미지의 색상을 Gray로 변경한 후 base64 문자열로 변환하여 사용 | LICENSE: Free for commercial use<br />프로젝트 Root에 Thumbnail 이미지 저장 |

## 사용방법

### 필수항목

아래의 항목들은 반드시 실행되어야 하며 사전에 Laravel [인증 스캐폴딩(Auth Scaffolding)](https://laravel.kr/docs/7.x/authentication#introduction)이 프로젝트에 추가되어 있어야 합니다.

#### 1. Package install

Laravel 7.x가 설치된 프로젝트 디렉터리 내에서 `composer` 명령어를 이용해 설치합니다.

```bash
composer require inium/laraboard
```

#### 2.  Laravel 프로젝트에 게시판 필수 파일 publish

`vendor:publish` 명령어를 이용해 게시판 필수 파일들을 publish 합니다. 

```bash
php artisan vendor:publish --tag=laraboard.essentials
```

publish 되는 파일은 아래와 같습니다.

- 게시판 설정파일: `config/laraboard.php` 로 생성됩니다.
- 데이터베이스 Migrations: `database/migrations` 파일에 게시판 데이터베이스 Schema 가 정의된 파일들이 복사됩니다.

#### 3. 게시판 테이블 마이그레이션

`/database/migrations` 디렉터리 내에 publish된 게시판 Table을 마이그레이션(migration) 합니다.

```bash
php artisan migrate
```

#### 4. 게시판 사용자 추가 trait 삽입

본 게시판 패키지는 Laravel의 인증(Auth) 기능을 이용합니다. 그래서 회원가입 시 인증 스캐폴딩(Auth Scaffolding)으로 생성된 Laravel 프로젝트 내 `/app/Http/Controllers/Auth/RegisterController.php`에 아래와 같이 게시판 사용자로 등록하는 `AuthRegisteredTrait`의 추가가 필요합니다.

```php
use Inium\Laraboard\Support\Auth\AuthRegisteredTrait; // 코드 추가

// RegisterController에 코드 추가
use AuthRegisteredTrait {
  AuthRegisteredTrait::registered insteadof RegistersUsers;
}
```

`AuthRegisteredTrait`은 회원가입 완료 후 해당 사용자를 게시판 사용자로 추가하는 코드입니다 (게시판 사용자는 별도의 Table에 저장). 이 코드는 `RegistersUsers` trait 의 `registered` 메소드를 대체합니다. 닉네임 중복방지 차원에서 {사용자이름_5자리해시코드} 로 구성된 닉네임을 자동으로 생성하여 저장합니다.

*cf. `registered` 메소드는  회원가입 시 사용자 추가가 완료되면 호출되는 메소드입니다.*

#### 5. 게시판 생성 및 접속 확인

본 게시판 패키지는 아래와 같이 명령어를 통해 게시판을 생성할 수 있습니다.

```bash
php artisan laraboard:board-create {boardName}
```

boardName은 게시판 이름이고 영문이며 unique 속성을 갖습니다. 본 명령어는 기본적인 게시판만을 생성하기 때문에 세부적인 정보는 사용자가 데이터베이스에서 직접 수정을 해야 합니다.

게시판 생성이 완료되면 Console 에 게시판 생성 완료 메시지와 함께 게시판 상대 경로를 출력합니다. 예시는 아래와 같습니다.

```bash
php artisan laraboard:board-create testboard
Board "testboard" is created. Now can use /board/testboard.
```

### 선택항목

#### 1 . 테스트 데이터 추가

본 게시판 패키지는 게시판 테스트 데이터가 구현되어 있습니다. 아래의 명렁어로 테스트 데이터를 추가 할 수 있습니다 (약간의 시간이 소요됩니다).

```bash
php artisan db:seed --class=Inium\\Laraboard\\Database\\Seeds\\BoardSeeder
```

- 테스트 데이터는 라라벨의 인증 스캐폴딩으로 생성된 `users` 테이블의 데이터도 함께 생성됩니다. 생성 시 인증 스캐폴딩으로 만들어진 `/database/factories/UserFactory.php` 내 정의된 factory를 이용합니다.

테스트 데이터 추가가 완료되면 free 라는 이름을 가진 자유게시판과 해당 게시판의 공지사항, 게시글, 댓글이 생성됩니다. 생성 완료 후 ( `config/laraboard.php`  의 설정을 하지 않은 경우) `/board/free` URL 접속하면 자유게시판의 게시글 목록이 출력되어 확인 가능합니다.

#### 2. 사용자 역할(Role)만 추가

게시판 사용자 역할만을 추가할 시 아래의 명령어로 추가할 수 있습니다.

```bash
php artisan db:seed --class=Inium\\Laraboard\\Database\\Seeds\\BoardRoleSeeder
```

#### 3. View 파일 Publish

본 게시판 패키지는 blade 템플릿을 이용해 기본적인 Layout만 구현되어 있습니다. 게시판 기능의 Layout을 수정하기 위해선 아래 명령어를 이용해 View 파일을 publish 한 후 직접 수정을 해야 합니다.

```bash
php artisan vendor:publish --tag=laraboard.resources
```

위의 명령어는 소스코드의 View 와 관련된 모든 리소스 파일들을 Laravel 프로젝트의 `/resources/views/vendor/laraboard` 디렉터리에 생성합니다.

## 주요사항

### 사용자와 역할(Roles)

#### 사용자

본 게시판 패키지의 회원가입과 사용자 정보는 Laravel의 [인증 스캐폴딩(Auth Scaffolding)](https://laravel.kr/docs/7.x/authentication#introduction)을 이용하며 게시판 사용자 정보를 인증 스캐폴딩의 사용자 정보(User)와 같이 저장합니다. 게시판 사용자 정보는 {닉네임, 썸네일 경로, 스캐폴딩의 사용자(User) ID, 사용자 역할(Role) ID} 로 구성되어 있습니다.

Laravel 인증 스캐폴딩의 `RegisterController`에 개발자가 게시판 사용자 등록 Trait을 추가하여 회원가입과 동시에 게시판 사용자 등록을 할 수 있습니다. Laravel의 회원가입 스캐폴딩은 별도로 닉네임을 입력하지 않기 때문에 본 게시판 패키지에서는 사용자 이름을 사용하도록 하였으며 중복을 허용하지 않기 때문에 5자리 해시코드를 추가하여 {사용자이름_해시코드5자리} 형태로 생성합니다.

게시판 사용자의 회원 정보 수정 기능은 구현되어 있지 않습니다.

#### 역할(Role)

본 게시판 패키지의 역할(Role)은 게시판 사용자의 접근 권한입니다. 접근 권한, 즉 역할은 게시글 읽기 / 쓰기, 댓글 읽기 / 쓰기가 가능한지의 여부를 판별하는데 사용합니다. 역할 정보는 {역할 ID, 역할 이름, 역할 설명, 관리자 여부}로 구성되어 있으며 접근권한 ID가 낮을수록 많은 접근을 허용합니다. 

역할 예시는 아래와 같습니다 (댓글도 동일).

- 사용자 역할 ID는 11, 게시판 A의 게시글 읽기 역할 ID가 10, 쓰기 역할 ID가 8일 경우: 게시글 읽기, 쓰기 불가능
- 사용자 역할 ID는 10, 게시판 A의 게시글 읽기 역할 ID가 10, 쓰기 역할 ID가 8일 경우: 게시글 읽기 가능, 쓰기 불가능
- 사용자 역할 ID는 9, 게시판 A의 게시글 읽기 역할 ID가 10, 쓰기 역할 ID가 8일 경우: 게시글 읽기 가능, 쓰기 불가능
- 사용자 역할 ID는 8, 게시판 A의 게시글 읽기 역할 ID가 10, 쓰기 역할 ID가 8일 경우: 게시글 읽기 가능, 쓰기 가능
- 사용자 역할 ID는 7, 게시판 A의 게시글 읽기 역할 ID가 10, 쓰기 역할 ID가 8일 경우: 게시글 읽기 가능, 쓰기 가능

관리자 여부는 게시글 작성 시 공지글 작성 여부 판별에 사용합니다.

### 게시글 / 댓글

본 게시판 패키지의 게시글과 댓글의 동작과정은 게시판의 게시글 읽기 / 쓰기, 댓글 읽기 / 쓰기 역할(Role)에 따릅니다. 단 게시글 / 댓글의 수정, 삭제는 본인만 가능합니다.

관리자는 공지 글의 작성이 가능합니다. 

댓글은 2 Depth 만을 지원합니다.

### Timezone

본 게시판 패키지의 Timezone은 Laravel 프로젝트의 설정파일인 `config/app.php`에 지정된 Timezone을 이용합니다.

### 파일 업로드

본 게시판 패키지는 별도의 파일 업로드 기능이 구현되어 있지 않습니다.

### 관리

본 게시판 패키지의 관리 페이지는 구현되어 있지 않습니다.

## 설정

게시판 설정 파일은 게시판 필수 파일 publish 완료 후 생성되는 `/config/laraboard.php` 에 정의되어 있으며 route와 board 설정으로 구성되어 있습니다. 각 설정 항목은 아래와 같습니다.

```php
/**
 * 게시판 라우트 설정
 */
'route' => [

  /**
   * 미들웨어에서 사용할 라우트 리스트
   */
  'middleware' => [
    'web'
  ],

  /**
   * 게시판 라우트 prefix. prefix가 'im' 일 경우, /im/board 형태로 생성.
   */
  'prefix' => ''
],

/**
 * 게시판 설정
 */
'board' => [

  /**
   * 사용자 정보 수집 여부. true (수집), false (수집하지 않음)
   * 
   * 수집항목:
   * - IP Address(암호화 하여 저장)
   * - User Agent(암호화 하여 저장)
   * - User Agent 분석 Device Type (desktop, tablet, mobile, other 중 1)
   * - OS name, version
   * - Browser name, version
   */
  'collect_user_info' => false,

  /*
   * 게시판 테이블 이름
   */
  'table_name' => [

    // 게시판 사용자 권한 테이블 이름
    'role' => 'lb_board_user_roles',

    // 게시판 사용자 테이블 이름
    'user' => 'lb_board_users',

    // 게시판 테이블 이름
    'board' => 'lb_boards',

    // 게시글 테이블 이름
    'post' => 'lb_board_posts',

    // 댓글 테이블 이름
    'comment' => 'lb_board_post_comments'

  ]
]
```

## License

MIT
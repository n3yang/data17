<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

use app\assets\AppAsset;
AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>



    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#"><!-- <img src="/img/logo.png" /> --></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="/">首页</a></li>
            <?
                if (!Yii::$app->user->isGuest) {
                    echo '<li>'
                        . Html::beginForm(['/site/logout'], 'post')
                        . Html::submitButton(
                            'Logout (' . Yii::$app->user->identity->username . ')',
                            ['class' => 'btn btn-link']
                            )
                        . Html::endForm()
                        . '</li>';
                }
            ?>
          </ul>
          <!-- 
          <form class="navbar-form navbar-right">
            <input type="text" class="form-control" placeholder="Search...">
          </form>
          -->
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-2 col-md-2 sidebar">

<?

$navItemsMember = [
    [
        'label' => '用户中心',
        'url' => Url::toRoute('/member/info'),
        // 'active' => true,
    ],
    [
        'label' => '账号安全设置',
        'url' => Url::toRoute('/member/security'),
    ],
    [
        'label' => '添加业务标签',
        'url' => Url::toRoute('/member/tag'),
    ],
    [
        'label' => 'API 使用向导',
        'url' => Url::toRoute('/member/api-guide'),
    ],
];

$navItemsAdmin = [
    [
        'label' => '用户列表',
        'url' => Url::toRoute('/admin/index'),
        // 'active' => true,
    ],
    [
        'label' => '添加用户',
        'url' => Url::toRoute('/admin/create'),
    ],
    [
        'label' => '请求日志',
        'url' => Url::toRoute('/api-log/index'),
    ],
];


$navItems = Yii::$app->user->identity && Yii::$app->user->identity->isAdmin()
    ? array_merge($navItemsAdmin , $navItemsMember)
    : $navItemsMember;
$navItems =    array_merge($navItemsAdmin , $navItemsMember);
preg_match('/([^\/]+\/?[^\/]+)/', Yii::$app->request->pathInfo, $matches);
foreach ($navItems as $key=>$v) {
  if ($v['url'] == '/' . $matches[1]) {
    $navItems[$key]['active'] = true;
  }
}

echo Nav::widget([
    'items' => $navItems,
    'options' => ['class' =>'nav nav-sidebar'], // set this to nav-tab to get tab-styled navigation
]);
?>

        </div>
        <div class="col-sm-10 col-sm-offset-2 col-md-10 col-md-offset-2 main">

          <?= $content ?>

        </div>
      </div>
    </div>



<?php $this->endBody() ?>
<style type="text/css">

/*
 * Base structure
 */

/* Move down content because we have a fixed navbar that is 50px tall */
body {
  padding-top: 50px;
}


/*
 * Global add-ons
 */

.sub-header {
  padding-bottom: 10px;
  border-bottom: 1px solid #eee;
}

/*
 * Top navigation
 * Hide default border to remove 1px line.
 */
.navbar-fixed-top {
  border: 0;
}

/*
 * Sidebar
 */

/* Hide for mobile, show later */
.sidebar {
  display: none;
}
@media (min-width: 768px) {
  .sidebar {
    position: fixed;
    top: 51px;
    bottom: 0;
    left: 0;
    z-index: 1000;
    display: block;
    padding: 20px;
    overflow-x: hidden;
    overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
    background-color: #f5f5f5;
    border-right: 1px solid #eee;
  }
}

/* Sidebar navigation */
.nav-sidebar {
  margin-right: -21px; /* 20px padding + 1px border */
  margin-bottom: 20px;
  margin-left: -20px;
}
.nav-sidebar > li > a {
  padding-right: 20px;
  padding-left: 20px;
}
.nav-sidebar > .active > a,
.nav-sidebar > .active > a:hover,
.nav-sidebar > .active > a:focus {
  color: #fff;
  background-color: #428bca;
}


/*
 * Main content
 */

.main {
  padding: 20px;
}
@media (min-width: 768px) {
  .main {
    padding-right: 40px;
    padding-left: 40px;
  }
}
.main .page-header {
  margin-top: 0;
}


/*
 * Placeholder dashboard ideas
 */

.placeholders {
  margin-bottom: 30px;
  text-align: center;
}
.placeholders h4 {
  margin-bottom: 0;
}
.placeholder {
  margin-bottom: 20px;
}
.placeholder img {
  display: inline-block;
  border-radius: 50%;
}

.navbar-inverse .navbar-nav > li > form > button {
  padding-top: 15px;
  padding-bottom: 15px;
}

</style>
</body>
</html>
<?php $this->endPage() ?>
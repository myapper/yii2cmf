<?php
/* @var $this yii\web\View */
/* @var $commentModel common\models\Comment */
/* @var $hots common\models\Article */
/* @var $commentModels common\models\Comment */
/* @var $pages yii\data\Pagination */
use yii\helpers\Html;
$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->category,'url' => ['/article/index', 'cid' => $model->category_id]];
$this->params['breadcrumbs'][] = $model->title;
?>
<div class="col-lg-9">
    <div class="view-title">
        <h1><?= $model->title ?></h1>
    </div>
    <?php if(stripos(Yii::$app->request->headers->get('User-Agent'), 'MicroMessenger') === false): ?>
    <div class="action">
        <span class="user"><a href="/user/31325"><span class="fa fa-user"></span> <?= $model->author?></a></span>
        <span class="time"><span class="fa fa-clock-o"></span> <?= date('Y-m-d H:i', $model->created_at) ?></span>
        <span class="views"><span class="fa fa-eye"></span> <?= $model->view?>次浏览</span>
        <span class="comments"><a href="#comments"><span class="fa fa-comments-o"></span> <?=$model->comment?>条评论</a></span>
        <span class="favourites"><a href="/favourite?type=extension&amp;id=601" title="" data-toggle="tooltip" data-original-title="收藏"><span class="fa fa-star-o"></span> <em>0</em></a></span>
        <span class="vote"><a class="up" href="<?=\yii\helpers\Url::to(['/vote','id'=>$model->id, 'type'=>'article', 'action'=>'up'])?>" title="" data-toggle="tooltip" data-original-title="顶"><span class="fa fa-thumbs-o-up"></span> <em><?=$model->up?></em></a><a class="down" href="<?=\yii\helpers\Url::to(['/vote','id'=>$model->id, 'type'=>'article', 'action'=>'down'])?>" title="" data-toggle="tooltip" data-original-title="踩"><span class="fa fa-thumbs-o-down"></span> <em><?=$model->down?></em></a></span>
    </div>
    <?php endif; ?>
    <!--内容-->
    <div class="view-content"><?= \yii\helpers\Markdown::process($model->content) ?></div>
    <?php if(stripos(Yii::$app->request->headers->get('User-Agent'), 'MicroMessenger') === false):?>
        <div class="well">带到手机上看<?= Html::img(\yii\helpers\Url::to(['/qrcode', 'text'=>Yii::$app->request->absoluteUrl])) ?></div>
    <?php endif;?>

    <!--分享-->
    <?= \common\widgets\share\Share::widget()?>
    <!--评论-->
    <div id="comments">
        <h4>共 <span class="text-danger"><?=$model->comment?></span> 条评论</h4>
        <div class="col-4">
            <ul class="media-list">
                <?php foreach($commentModels as $item):?>
                <li class="media" data-key="<?=$item->id?>">
                    <div class="media-left">
                        <a href="#">
                            <img class="media-object" src="http://www.yiichina.com/uploads/avatar/000/03/21/32_avatar_small.jpg" alt="...">
                        </a>
                    </div>
                    <div class="media-body">
                        <div class="media-heading"><a href=""><?=$item->user->username?></a> 评论于 <?=date('Y-m-d H:i', $item->created_at)?></div>
                        <div class="media-content"><?= $item->content?></div>
                        <?php foreach($item->sons as $son):?>
                        <div class="media">
                            <div class="media-left">
                                <a href="/user/index/1.html" rel="author" title=""><img class="media-object" src="http://www.yiichina.com/uploads/avatar/000/03/21/32_avatar_small.jpg" alt=""></a>
                            </div>
                            <div class="media-body">
                                <div class="media-heading">
                                    <a href="/user/index/1.html" rel="author" data-original-title="<?=$son->user->username?>" title=""><?=$son->user->username?></a> 回复于 <?=date('Y-m-d H:i', $son->created_at)?>
                                    <span class="pull-right"><a class="reply-btn j_replayAt" href="javascript:;">回复</a></span>
                                </div>
                                <div class="media-content"><?= $son->content?></div>
                            </div>
                        </div>
                        <?php endforeach;?>
                        <div class="media-action">
                            <a class="reply-btn" href="#">回复</a><span class="vote"><a class="up" href="<?=\yii\helpers\Url::to(['/vote','id'=>$item->id, 'type'=>'comment', 'action'=>'up'])?>" title="" data-toggle="tooltip" data-original-title="顶"><span class="fa fa-thumbs-o-up"></span> <em><?=$item->up?></em></a><a class="down" href="<?=\yii\helpers\Url::to(['/vote','id'=>$item->id, 'type'=>'comment', 'action'=>'down'])?>" title="" data-toggle="tooltip" data-original-title="踩"><span class="fa fa-thumbs-o-down"></span> <em><?=$item->down?></em></a></span>
                        </div>
                    </div>
                </li>
                <?php endforeach;?>
            </ul>
            <?= \yii\widgets\LinkPager::widget([
                'pagination' => $pages,
            ]); ?>
        </div>
    </div>
    <h4>发表评论</h4>
    <?php if(!Yii::$app->user->isGuest): ?>
        <?php $form = \yii\widgets\ActiveForm::begin(['action'=>\yii\helpers\Url::toRoute('comment/create')]); ?>
        <?= $form->field($commentModel, 'content')->label(false)->widget('\yidashi\markdown\Markdown',['options'=>['style'=>'height:200px;']]); ?>
        <?= Html::hiddenInput(Html::getInputName($commentModel,'article_id'), $model->id) ?>
        <div class="form-group">
            <?= Html::submitButton('提交',['class'=>'btn btn-primary']) ?>
        </div>
        <?php \yii\widgets\ActiveForm::end(); ?>
        <!--回复-->
        <?php $form = \yii\widgets\ActiveForm::begin(['action'=>\yii\helpers\Url::toRoute('comment/create'), 'options'=>['class'=>'reply-form hidden']]); ?>
        <?= Html::hiddenInput(Html::getInputName($commentModel,'article_id'), $model->id) ?>
        <?= Html::hiddenInput(Html::getInputName($commentModel,'parent_id'), 0, ['class'=>'parent_id']) ?>
        <?=$form->field($commentModel, 'content')->label(false)->textarea()?>
        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary">回复</button>
        </div>
        <?php \yii\widgets\ActiveForm::end(); ?>
    <?php else: ?>
        <div class="well">您需要登录后才可以评论。<?=Html::a('登录',['site/login'])?> | <?=Html::a('立即注册', ['site/signup'])?></div>
    <?php endif; ?>
</div>
<div class="col-lg-3">
    <div class="panel panel-default">
        <div class="panel-heading">
            热门<?=$model->category?>
        </div>
        <div class="panel-body">
            <ul class="post-list">
                <?php foreach($hots as $item):?>
                <li><?=Html::a($item->title,['/article/view','id'=>$item->id])?></li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
</div>
<?= \common\widgets\danmu\Danmu::widget(['id'=>$model->id]);?>
<?php $this->registerJs(<<<js
    $(function(){
        $('.view-content iframe').addClass('embed-responsive-item').wrap('<div class="embed-responsive embed-responsive-16by9"></div>');
    });
js
);
if(stripos(Yii::$app->request->headers->get('User-Agent'), 'MicroMessenger') === false) {
    $this->registerJsFile('http://res.wx.qq.com/open/js/jweixin-1.0.0.js');
    $this->registerJs(<<<js
wx.onMenuShareAppMessage({
    title: '{$model->title}', // 分享标题
    desc: '{$model->desc}', // 分享描述
    link: location.href, // 分享链接
    imgUrl: '{$model->cover}', // 分享图标
    success: function () {
        // 用户确认分享后执行的回调函数
    },
    cancel: function () {
        // 用户取消分享后执行的回调函数
    }
});
js
);
}
?>

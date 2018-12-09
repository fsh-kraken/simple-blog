<?php
require 'header.php';

if(!Session::isLogin()){
	Redirect::to('../../login.php');
}

$user = Session::get(Config::get('session/profile'));
?>


		<!-- Page Header -->
		<header class="masthead" style="background-image: url('<?= Config::get('address/image') ?>home-bg.jpg')">
			<div class="overlay"></div>
			<div class="container">
				<div class="row">
					<div class="col-lg-8 col-md-10 mx-auto">
						<div>
							
						</div>
						<div class="site-heading">
						<img src="<?= Config::get('address/image').'avatar.png' ?>" alt="" style="width:200px;height:200px;">
							<h1> <?= title_case($user->fullname) ?> </h1>
							<span class="subheading"> <?= $user->username ?> </span>
						</div>
					</div>
				</div>
			</div>
		</header>

	<?php 
		// $e = Query::fetchAll('posts',['user_id','=','1'])->results(); 
		$e = (new Query())->select('posts')->where('user_id','=',$user->id)->orderBy('created_at','desc')->get();
	?>

		<!-- Main Content -->
		<div class="container">
		<div class="row">
			<div class="col-lg-8 col-md-10 mx-auto">
				<form action="actions/write-post.php" method='post'>
				<div class="<?= Request::get('class') ?>" role="alert">
					<?= Request::get('message') ?>
				</div> 
				<div class="form-group">
					<label for="title" >Title</label>
					<input type="text" name="title" id="title" class="form-control">
				</div>
				<div class="form-group">
					<label for="post" >Write a topic</label>
					<textarea name="post" id="post" class="form-control" cols="" rows=""></textarea>
				</div>
				<button type="submit" class="btn btn-primary">Publish</button>
				</form>
			</div>
		</div>
		<br><br>
				<div class="row">
					<div class="col-lg-8 col-md-10 mx-auto">
					<?php if($e): ?>
						<?php foreach($e as $post): ?>
							<div class="post-preview">
								<!-- <a href="post.html"> -->
								<h2 class="post-title">
									<?= $post->title ?>
								</h2>
								<p class="post-subtitle">
									<?= $post->post ?>
								</p>
								<!-- </a> -->
								<p class="post-meta">Posted by
								<span>
									<?= $post->user_id == $user->id ? 'me' : (new Query())->select('users')->where('id',$post->user_id)->first()->username ?>
								</span>
								on <?= readable_date($post->created_at) ?></p>
							</div>

							<!-- comments -->
							<div>
								<div><small>Comments:</small></>
								<div style="padding-left:2em;">
									<?php 
										$comments = (new Query())->select('comments')->where('post_id','=',$post->id)->get();
									?>
									<?php if($comments): ?>
									<?php foreach($comments as $comment): ?>
										<div style="border-bottom:1px solid #ddd;"> 
											<small>
												<?= (new Query())->select('users')->where('id','=',$comment->user_id)->first()->fullname ?> : 
												<?= $comment->comment ?>
												<br>
												<span style='font-size:10px;font-style:italic'> Date : <?= readable_datetime($comment->created_at) ?> </span>
											</small> 
										</div>
									<?php endforeach; ?>
									<?php endif; ?>
								</div>
							</div>

							<!-- comment form -->
							<?php if($user): ?>
							<div>
								<div><small>Leave a comment</small></div>
								<div id="form">
									<div class="form-group">
										<textarea name="post" id="post" class="form-control" cols="" rows=""></textarea>
									</div>
									<button type="submit" data-id="<?=$post->id?>" class="btn btn-primary leave-comment">Reply</button>
								</div>
							</div>
							<?php endif; ?>

							<hr>
						<?php endforeach; ?>
					<?php else: ?>
						<div class="post-preview">
							<p class="post-subtitle">
								No posts yet
							</p>
						</div>
					<?php endif; ?>
					</div>
				</div>
		</div>

		<hr>

		<?php include 'footer.php'; ?>


		<script>
		$('.leave-comment').click(function(){
			let id = $(this).data('id');
			let comment = $(this).siblings('div').children('textarea').val();
			
			if(comment == "") return false;

			$.ajax({
            	method: "POST",
				url: "actions/comment.php",
				data: { 
					comment : comment,
					post_id : id,
					user_id : "<?= $user->id ?>"
				},
				success: function(data){
					location.reload();
				}
			})
		});
	</script>
	</body>

</html>
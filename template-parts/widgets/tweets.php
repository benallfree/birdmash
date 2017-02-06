<?php if ( ! empty( $tweets ) ) : ?>
<script type="text/javascript" async src="https://platform.twitter.com/widgets.js"></script>
<?php echo wp_kses_post( $args['before_widget'] ); ?>
<?php echo wp_kses_post( $args['before_title'] ); ?>
	<?php echo wp_kses_post( $instance['title'] ); ?>
<?php echo wp_kses_post( $args['after_title'] ); ?>
<?php foreach ( $tweets as $tweet ) : ?>
	<article class="tweet">
		<header>
			<a class="userinfo" href="http://twitter.com/<?php echo esc_url( $tweet['username'] ); ?>">
				<img class="avatar" src="<?php echo esc_url( $tweet['avatar'] ); ?>" alt="Twitter avatar">
				<span class="full-name"><?php echo esc_html( $tweet['name'] ); ?></span>
				<span class="username">@<?php echo esc_html( $tweet['username'] ); ?></span>
			</a>
			<a class="logo" href="http://twitter.com">
				<svg id="Logo_FIXED" data-name="Logo — FIXED" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><style>.cls-1{fill:none;}.cls-2{fill:#1da1f2;}</style></defs><title>Twitter_Logo_Blue</title><rect class="cls-1" width="400" height="400"/><path class="cls-2" d="M153.62,301.59c94.34,0,145.94-78.16,145.94-145.94,0-2.22,0-4.43-.15-6.63A104.36,104.36,0,0,0,325,122.47a102.38,102.38,0,0,1-29.46,8.07,51.47,51.47,0,0,0,22.55-28.37,102.79,102.79,0,0,1-32.57,12.45,51.34,51.34,0,0,0-87.41,46.78A145.62,145.62,0,0,1,92.4,107.81a51.33,51.33,0,0,0,15.88,68.47A50.91,50.91,0,0,1,85,169.86c0,.21,0,.43,0,.65a51.31,51.31,0,0,0,41.15,50.28,51.21,51.21,0,0,1-23.16.88,51.35,51.35,0,0,0,47.92,35.62,102.92,102.92,0,0,1-63.7,22A104.41,104.41,0,0,1,75,278.55a145.21,145.21,0,0,0,78.62,23"/></svg>
			</a>
		</header>
		<div class="body">
			<?php echo wp_kses_post( $tweet['text'] ); ?>
		</div>
		<footer class="intents">
			<div class="meta">
				<?php echo esc_html( $tweet['created_at'] ); ?>
			</div>
			<div class="intents">
				<a class="reply" href="<?php echo esc_url( "https://twitter.com/intent/tweet?in_reply_to={$tweet['id']}" ); ?>" target="_new">
					<svg width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 65 72">
						<path d="M41 31h-9V19c0-1.14-.647-2.183-1.668-2.688-1.022-.507-2.243-.39-3.15.302l-21 16C5.438 33.18 5 34.064 5 35s.437 1.82 1.182 2.387l21 16c.533.405 1.174.613 1.82.613.453 0 .908-.103 1.33-.312C31.354 53.183 32 52.14 32 51V39h9c5.514 0 10 4.486 10 10 0 2.21 1.79 4 4 4s4-1.79 4-4c0-9.925-8.075-18-18-18z"/>
					</svg>
				</a>
				<a class="retweet" href="<?php echo esc_url( "https://twitter.com/intent/retweet?tweet_id={$tweet['id']}" ); ?>" target="_new">
					<svg width="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 75 72">
						<path d="M70.676 36.644C70.166 35.636 69.13 35 68 35h-7V19c0-2.21-1.79-4-4-4H34c-2.21 0-4 1.79-4 4s1.79 4 4 4h18c.552 0 .998.446 1 .998V35h-7c-1.13 0-2.165.636-2.676 1.644-.51 1.01-.412 2.22.257 3.13l11 15C55.148 55.545 56.046 56 57 56s1.855-.455 2.42-1.226l11-15c.668-.912.767-2.122.256-3.13zM40 48H22c-.54 0-.97-.427-.992-.96L21 36h7c1.13 0 2.166-.636 2.677-1.644.51-1.01.412-2.22-.257-3.13l-11-15C18.854 15.455 17.956 15 17 15s-1.854.455-2.42 1.226l-11 15c-.667.912-.767 2.122-.255 3.13C3.835 35.365 4.87 36 6 36h7l.012 16.003c.002 2.208 1.792 3.997 4 3.997h22.99c2.208 0 4-1.79 4-4s-1.792-4-4-4z"/>
					</svg>
				</a>
				<a class="like" href="<?php echo esc_url( "https://twitter.com/intent/like?tweet_id={$tweet['id']}" ); ?>" target="_new">
					<svg width="15" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 54 72">
						<path d="M38.723,12c-7.187,0-11.16,7.306-11.723,8.131C26.437,19.306,22.504,12,15.277,12C8.791,12,3.533,18.163,3.533,24.647 C3.533,39.964,21.891,55.907,27,56c5.109-0.093,23.467-16.036,23.467-31.353C50.467,18.163,45.209,12,38.723,12z"/>
					</svg>
				</a>
			</div>
		</footer>
	</article>
<?php endforeach; ?>
<script>
	!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
</script>
<?php echo wp_kses_post( $args['after_widget'] ); ?>
<?php endif; ?>

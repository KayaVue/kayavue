<?php
/**
 * Site footer.
 *
 * @package KayaVue
 */
?>
<footer class="site-footer">
	<div class="wrap footer-grid">
		<div>
			<p class="wordmark wordmark-footer"><span class="word">Kaya<span>Vue</span></span></p>
			<p>A consumer guide to aesthetic procedures in Indian cities. Read the fee range, the recovery, and the stories. Then talk to a clinician who has examined you.</p>
		</div>
		<div>
			<h2>Explore</h2>
			<?php kayavue_primary_nav('footer-list'); ?>
		</div>
		<div>
			<h2>The fine print</h2>
			<ul class="footer-list">
				<li><a href="<?php echo esc_url(home_url('/about/')); ?>">About KayaVue</a></li>
				<li><a href="<?php echo esc_url(home_url('/for-clinics/')); ?>">For clinics</a></li>
				<li><a href="<?php echo esc_url(home_url('/privacy/')); ?>">Privacy</a></li>
				<li><a href="<?php echo esc_url(home_url('/medical-disclaimer/')); ?>">Medical disclaimer</a></li>
			</ul>
		</div>
	</div>
	<div class="wrap footer-note">
		<p>Demo build. Clinics, doctors, and reviews are fictional. Diagrams are drawings, not patient photographs. KayaVue does not provide medical advice. Results vary.</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

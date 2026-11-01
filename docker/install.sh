#!/bin/sh
set -eu

echo "Waiting for WordPress files and wp-config.php…"
i=0
while [ ! -f /var/www/html/wp-load.php ] || [ ! -f /var/www/html/wp-config.php ]; do
  i=$((i + 1))
  if [ "$i" -gt 90 ]; then
    echo "Timed out waiting for the WordPress container to initialise." >&2
    exit 1
  fi
  sleep 2
done

if [ ! -f /var/www/html/wp-content/themes/kayavue/style.css ]; then
  echo "The KayaVue theme is not mounted at wp-content/themes/kayavue." >&2
  exit 1
fi

echo "Waiting for MySQL…"
php -r '
mysqli_report(MYSQLI_REPORT_OFF);
$host = getenv("WORDPRESS_DB_HOST") ?: "db:3306";
$parts = explode(":", $host);
$h = $parts[0];
$port = isset($parts[1]) ? (int) $parts[1] : 3306;
$user = getenv("WORDPRESS_DB_USER") ?: "kayavue";
$pass = getenv("WORDPRESS_DB_PASSWORD") ?: "kayavue";
$name = getenv("WORDPRESS_DB_NAME") ?: "kayavue";
for ($i = 0; $i < 60; $i++) {
    try {
        $mysqli = mysqli_init();
        mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 3);
        $connected = @mysqli_real_connect($mysqli, $h, $user, $pass, $name, $port);
        if ($connected) {
            exit(0);
        }
        if (!$mysqli->connect_errno) {
            exit(0);
        }
    } catch (Throwable $e) {
        // Database is still starting.
    }
    sleep(2);
}
fwrite(STDERR, "MySQL did not accept connections.\n");
exit(1);
'

if ! wp core is-installed >/dev/null 2>&1; then
  wp core install \
    --url="${WP_URL:-http://localhost:8080}" \
    --title="KayaVue" \
    --admin_user="${WP_ADMIN_USER:-kayavue}" \
    --admin_password="${WP_ADMIN_PASSWORD:-kayavue-demo}" \
    --admin_email="${WP_ADMIN_EMAIL:-demo@kayavue.local}" \
    --skip-email
fi

wp option update blogdescription "Compare aesthetic procedures, clinics, and patient stories across India" >/dev/null
wp option update timezone_string "Asia/Kolkata" >/dev/null
wp option update date_format "j M Y" >/dev/null
wp option update time_format "g:i a" >/dev/null
wp option update start_of_week 1 >/dev/null
wp option update default_comment_status closed >/dev/null
wp option update default_ping_status closed >/dev/null
wp option update blog_public 0 >/dev/null
wp option update show_on_front posts >/dev/null

wp theme activate kayavue
wp rewrite structure '/%postname%/' --hard
wp kayavue seed
wp rewrite flush --hard

echo ""
echo "KayaVue is installed."
echo "Site:  ${WP_URL:-http://localhost:8080}"
echo "Admin: ${WP_URL:-http://localhost:8080}/wp-admin"
echo "User:  ${WP_ADMIN_USER:-kayavue}"

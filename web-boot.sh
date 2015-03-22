# Apache configuration
# Modifications to conf/apache2/heroku.conf from the Heroku PHP buildpack
echo "ServerSignature Off" >> /app/vendor/heroku/heroku-buildpack-php/conf/apache2/heroku.conf
echo "ServerTokens Prod" >> /app/vendor/heroku/heroku-buildpack-php/conf/apache2/heroku.conf

# Launch Apache with PHP
# Set web root to folder public_html
vendor/bin/heroku-php-apache2
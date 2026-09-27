FROM php:8.2-apache

# mbstring is used by rsvp.php (mb_substr) for correctly trimming
# names/messages that may contain multi-byte characters.
RUN docker-php-ext-install mbstring

# Apache serves from /var/www/html by default — copy the app straight in
COPY ArifAmira/ /var/www/html/

# The RSVP wall writes to data/rsvp.json, so make sure that's writable.
# NOTE: Render's default disk is ephemeral — see DEPLOY.md for details
# on what that means for RSVP data persistence.
RUN chown -R www-data:www-data /var/www/html/data \
    && chmod -R 775 /var/www/html/data

EXPOSE 80

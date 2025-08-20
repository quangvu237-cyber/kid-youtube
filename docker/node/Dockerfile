FROM node:22

# Set working directory
WORKDIR /var/www

# Copy startup script
COPY docker/node/startup.sh /usr/local/bin/startup.sh
RUN chmod +x /usr/local/bin/startup.sh

# Use startup script as entrypoint
ENTRYPOINT ["/usr/local/bin/startup.sh"]
CMD ["tail", "-f", "/dev/null"]

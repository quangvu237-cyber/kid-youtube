FROM node:22

# Set working directory
WORKDIR /var/www

# Copy package files trước để dùng cache khi dependencies không đổi
COPY package*.json ./

# Install dependencies
RUN npm install

# Copy toàn bộ source code (sau install để tránh phá cache install)
COPY . .

# Build frontend assets
RUN npm run build

# Giữ container sống nếu không override CMD
CMD ["tail", "-f", "/dev/null"]

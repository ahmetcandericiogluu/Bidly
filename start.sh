#!/bin/bash

# Build and run the entire application
echo "🚀 Building Bidly Application..."

# Stop any running containers
docker-compose down

# Build and start all services
docker-compose up --build -d

echo "✅ Application is running!"
echo "🌐 Frontend: http://localhost:8080"
echo "🔧 Backend API: http://localhost:8080/api"
echo "🗄️ Database: localhost:5433"

# Show running containers
docker-compose ps

@echo off
echo Initializing Git repository...
git init

echo Adding all files...
git add .

echo Creating first commit...
git commit -m "Initial commit: Simple Task API with CRUD operations"

echo Adding remote origin...
git remote add origin https://github.com/MinDeaDBIood/Simple-Task-API.git

echo Setting branch to main...
git branch -M main

echo Pushing to GitHub...
git push -u origin main

echo.
echo Done! Your project is now on GitHub.
pause
